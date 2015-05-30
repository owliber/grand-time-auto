<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename CronController.php
 */

class CronController extends Controller
{
    public $PID;
    public $PIDFile;
    public $PIDLog;
    
    public $_curdate;
    
    public function __construct() {
        $this->_curdate = date('Y-m-d H:i:s');
    }
    
    /**
     * Check if PID file exist
     * @return boolean
     */
    public function PID_exists()
    {
        $file = Yii::app()->file;
        $path = Yii::app()->basePath . '/runtime/';
        $this->PIDLog = $path . $this->PIDFile;
        
        if($file->set($this->PIDLog)->exists)
            return true;
        else
            return false;
    }
    
    /**
     * Create the PID file
     */
    public function createPID()
    {
        $file = Yii::app()->file;
        //Create pid file
        $pid = $file->set($this->PIDLog);
        $this->PID = $pid;
        
        $pid->create();
        $pid->setContents('owliber@yahoo.com', true);  
    }
           
    public function actionAutocomplete()
    {
        if(Tools::job_enabled())
        {
            $this->PIDFile = 'Autocomplete.PID';
            $cron_id = 1; //Payout - Autocomplete
            
            if(!$this->PID_exists())
            {
                //Create pid file      
                $this->createPID();
                $this->lap1run();
                $this->lap2run();
                $this->lap3run();
                $this->update_last_run($cron_id);
                $this->PID->delete();
                
                $status_msg = 'Scheduled run completed.';
            }
            else
            {
                $status_msg = 'Cron job is currently running.';
            }
            
        }
        else
        {
            $status_msg = 'Cron job is disabled.';
        }
        
        return $status_msg;
        
    }
    
    public function actionProcess()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            if(isset($_GET['render']) && !empty($_GET['render']))
            {
               $model = new Jobs();
               $this->PIDFile = 'Autocomplete.PID';
               $model->cron_id = 1;
               
               if(!$this->PID_exists())
               {
                    $retval = $this->actionAutocomplete();
                    $retcode = 0;
               }
               else
               {
                   $retcode = 1;
                   $retval = 'Scheduled job is currently running, please try again later.';
               }
               
               $lastrun = $model->get_last_run();
              
            }
            
            echo CJSON::encode(array('retval'=>$retval,'retcode'=>$retcode,'lastrun'=>$lastrun));
        }
    }
        
    public function lap1run()
    {
        $model = new Jobs();
        $queues = $model->get_queues();
        $lap_no = 1;
        
        if(count($queues)>0)
        {
            foreach($queues as $queue)
            {
                $model->job_id = $queue['job_queue_id'];
                $account_id = $queue['account_id'];
                $account_type_id = $queue['account_type_id'];
                
                //Get reference records
                $ref_table_count = Tools::get_value('TOTAL_TABLE_CLIENT_PER_ACCOUNT');
                $ref_min_client = Tools::get_value('MIN_CLIENT_FOR_PAYOUT');
                
                $table_count = $queue['table_count'];
                
                switch($table_count)
                {
                    case $ref_min_client: //Generate first payout
                    case 4://Possible total number of clients if base downlines are not yet filled.
                        $payout_code = $this->get_payout_code($account_type_id, $ref_min_client, $lap_no);
                        PayoutController::generatePayout($payout_code, $account_id);
                        break;
                    
                    case $ref_table_count: //Generate payout for table completion
                        //Insert client to lap_info as reference for table completion
                        $laps = new LapModel();
                        $laps->account_id = $account_id;
                        $laps->lapComplete($lap_no);
                        
                        //Advance to 2nd lap
                        LapsController::createLap(2,$account_id);
                        
                        //Increment total client for lap 2 network
                        Tools::add_client_count($account_type_id, 2);
                        
                        //Generate payout
                        $payout_code = $this->get_payout_code($account_type_id, $ref_table_count, $lap_no);
                        PayoutController::generatePayout($payout_code, $account_id);
                        break;
                }
                
                $model->process_queues();
            }
        }
        
        
    }
    
    public function lap2run()
    {
        /* Set lap no */
        $lap_no = 2;
        $model = new LapModel();
        $model->lap_no = $lap_no;
        $clients = $model->getLapCompleted();
        
        /* Get reference records */
        $ref_table_count = Tools::get_value('TOTAL_TABLE_CLIENT_PER_ACCOUNT');
        $newrows = array();
        
        if(count($clients)>0)
        {
            foreach($clients as $client)
            {
                $network = Network::getNetworkCount($client['client_id'], $lap_no);
                $client_count = count($network);

                if($client_count == $ref_table_count)
                {
                    $newrows[] = $client;
                }
            } 
        }
        
        /* Compute lap 2 accounts */
        if(count($newrows)>0)
        {
            foreach($newrows as $row)
            {
                //Complete lap
                $model->account_id = $row['client_id'];
                $model->lapComplete($lap_no);
                $account_type_id = $row['account_type_id'];
                
                //Generate payout
                $payout_code = $this->get_payout_code($account_type_id, $ref_table_count, $lap_no);
                PayoutController::generatePayout($payout_code, $row['client_id']);
                LapsController::createLap(3, $row['client_id']);
                
                //Increment client count for lap 3 network
                Tools::add_client_count($account_type_id, 3);
            }
        }                   
    }
    
    public function lap3run()
    {
        /* Set lap no */
        $lap_no = 3;
        $model = new LapModel();
        $payout = new PayoutModel();
        $model->lap_no = $lap_no;
        $payout->lap_no = $lap_no;
        $clients = $model->getLapCompleted();
        
        /* Get reference records */
        $checkpoint = Tools::get_value('LAP3_CHECKPOINT');
        
        if(count($clients)>0)
        {
            foreach($clients as $client)
            {
                $client_id = $client['client_id'];
                $account_type_id = $client['account_type_id'];
                $network = Network::getNetworkCount($client_id, $lap_no);
                $client_count = count($network);
                $model->account_id = $client_id;
                $payout->account_id = $client_id;
                
                if($client_count >= $checkpoint)
                {
                    $payout->head_count = $client_count;
                                        
                    if($client_count >= Tools::get_value('MIN_CLIENT_FOR_PAYOUT'))
                    {
                        $payout_code = $this->get_payout_code($account_type_id, $client_count, $lap_no);
                        PayoutController::generatePayout($payout_code, $client_id);
                        
                        if($client_count == Tools::get_value('TOTAL_TABLE_CLIENT_PER_ACCOUNT'));
                        {
                            $info = new Clients();
                            $info->account_id = $client_id;
                            $account = $info->getClientInfo();
                            $referrer_id = $account['referrer_id'];
                            $bonus_code = $this->get_bonus_code($account_type_id);
                            //Generate only if referrer_id is set
                            if(!empty($referrer_id))
                                PayoutController::generatePayout($bonus_code, $referrer_id);
                            $model->lapComplete($lap_no);
                        }
                    }
                    
                }
            } 
        }
                         
    }
    
    public function update_last_run($cron_id)
    {
        $model = new Jobs();
        $model->cron_id = $cron_id;
        $model->update_job_schedule();
    }
    
    public function get_payout_code($account_type_id, $head_count, $lap_no)
    {
        $model = new PayoutModel();
        $model->account_type_id = $account_type_id;
        $model->lap_no = $lap_no;
        $model->head_count = $head_count;
        $result = $model->getPayoutMatrixByAccountType();
        return $result['code'];
    }
    
    public function get_bonus_code($account_type_id)
    {
        $model = new PayoutModel();
        $model->account_type_id = $account_type_id;
        $result = $model->getPayoutMatrixBonus();
        return $result['code'];
    }
    
}


