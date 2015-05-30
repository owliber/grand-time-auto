<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 18, 2015
 * @filename LapsController.php
 */

class LapsController extends Controller
{
    public $layout = 'column2';
    public $dialogMessage;
    public $dialogOpen = false;
    public $dialogTitle;
    
    public function actionIndex()
    {
        $this->initialize();
        
        $model = new LapModel();
        $result = array();
        $newrows = array();
        
        if(isset($_POST['LapModel']))
        {
            if(isset(Yii::app()->session['LapModel'])) unset(Yii::app()->session['LapModel']);
            $model->attributes = $_POST['LapModel'];
            Yii::app()->session['LapModel'] = $model->attributes;
        }
        
        if(isset(Yii::app()->session['LapModel']) && !isset($_POST['LapModel']))
        {
            $model->attributes = Yii::app()->session['LapModel'];
        }        
         
        if($model->validate())
        {
            $model->lap_table = LapsController::getLapName($model->lap_no);
            $result = $model->getLapResults();

            foreach($result as $key=>$val)
            {
                $rows = $val;
                $clients = Network::getNetworkCount($val['account_id'], $val['lap_no']);
                $rows['total_clients'] = count($clients);
                $newrows[] = $rows;
            }
            
            $result = $newrows;
        }
        
        $dataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>50,
                                ),
                            ));
        
        $this->render('index',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider
        ));
    }
        
    public function actionFirst()
    {
        $model = new RegistrationForm();
        $clients = new Clients();
        
        if(isset($_GET['id']))
        {
            $clients->account_id = $_GET['id'];
        }
        else
        {
            if(Yii::app()->user->isClient())
            {
                $clients->account_id = Yii::app()->user->getUserId();
            }
            else
            {
                $reference = new ReferenceModel();
                $base_account_id = $reference->get_variable_value('JS_BASE_ACCOUNT_ID');
                $clients->account_id = $base_account_id;
            }
        }
        
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];
        $package = LapsController::getPackageName($clients->account_type_id);
                   
        $this->render('_first',array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
            'package'=>$package,
        ));
    }
    
    public function actionSecond()
    {
        $model = new RegistrationForm();
        $clients = new Clients();
        $view = '_second';
        
        if(Yii::app()->user->isClient())
        {            
            $model = new LapModel();
            $model->account_id = Yii::app()->user->getUserId();
            $result = $model->getLapInfo();
            
            if($result[0]['lap_no'] > 0)
                $clients->account_id = Yii::app()->user->getUserId();
            else
                $view = '_info';
        }
        else
        {
            if(isset($_POST['RegistrationForm']))
            {
                $model->attributes = $_POST['RegistrationForm'];
                
                if(empty($model->client_id))
                {
                    $this->dialogMessage = "Unable to locate the account.";
                    $this->dialogTitle = "Not Found";
                    $this->dialogOpen = true;
                }
                else
                {
                    $clients->account_id = $model->client_id;
                }
                
            }
            else
            {
                if(isset($_GET['id']))
                {
                    $clients->account_id = $_GET['id'];
                }
                else
                {   
                    $laps = new LapModel();
                    $laps->lap_no = 'lap_two';
                    $info = $laps->getBaseAccount();
                    if(count($info)>0)
                    {
                        $clients->account_id = $info[0]['client_id'];
                    }
                    else
                    {
                        $view = '_info';
                    }
                }
            }
            
        }
        
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];
        $package = LapsController::getPackageName($clients->account_type_id);
                   
        $this->render($view,array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
            'package'=>$package,
        ));
        
    }
    
    public function actionThird()
    {
        $model = new RegistrationForm();
        $clients = new Clients();
        $view = '_third';
        
        if(Yii::app()->user->isClient())
        {            
            
            $model = new LapModel();
            $model->account_id = Yii::app()->user->getUserId();
            $result = $model->getLapInfo();
            
            if($result[0]['lap_no'] > 1)
                $clients->account_id = Yii::app()->user->getUserId();
            else
                $view = '_info';
        }
        else
        {
            if(isset($_GET['id']))
            {
                $clients->account_id = $_GET['id'];
            }
            else
            {
                $laps = new LapModel();
                $laps->lap_no = 'lap_three';
                $info = $laps->getBaseAccount();
                if(count($info)>0)
                {
                    $clients->account_id = $info[0]['client_id'];
                }
                else
                {
                    $view = '_info';
                }
            }
        }
                
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];
        $package = LapsController::getPackageName($clients->account_type_id);
                   
        $this->render($view,array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
            'package'=>$package
        ));
        
    }
    
    public static function createLap($lap_no, $account_id)
    {
        $laps = new LapModel();
        $laps->new_account_id = $account_id;
        
        $clients = new Clients();
        $clients->account_id = $account_id;
        $clientinfo = $clients->getClientInfo();
        $account_type_id = $clientinfo['account_type_id'];
        
        switch($lap_no)
        {
            default:
                $lap_count = Network::getLapCount($lap_no,$account_type_id);
                
                //Check if first account set sponsor_id and position to null
                if($lap_count == 0)
                {
                    $laps->sponsor_id = NULL;
                    $laps->pos = NULL;
                }
                elseif($lap_count == 1 || $lap_count == 2  )
                {
                    $laps->lap_no = LapsController::getLapName($lap_no);
                    $laps->account_type_id = $account_type_id;
                    $info = $laps->getBaseAccount();
                    $sponsor_id = $info[0]['client_id'];
                    $lap_count == 1 ? $pos = 0 : $pos = 1;
                    $laps->pos = $pos;
                    $laps->sponsor_id = $sponsor_id;
                }
                else
                {
                    /* Lap count >= 3 */
                    if($lap_no == 2)
                    {
                        $laps->account_type_id = $account_type_id;
                        $laps->lap_no = $lap_no;
                        $info = $laps->getLapSlot();
                        $laps->sponsor_id = $info['client_id'];
                        $info['client_count'] == 1 ? $pos = 1 : $pos = 0;
                        $laps->pos = $pos;
                    }
                    
                    if($lap_no == 3)
                    {
                        $laps->lap_no = $lap_no;
                        
                        $client_count = LapsController::getSlotPos($account_type_id);
                        do {
                            $client_id = LapsController::getAvailableSlot($lap_no, $client_count, $account_type_id);
                            if($client_id === false) 
                            {
                                //Increment count by 1
                                $client_count++;
                                //($client_count == 6) ? $rowval = 2 : $rowval = $client_count;
                                if($client_count == 6)
                                {
                                    $rowval = 2;
                                    $client_count = 2;
                                }
                                else
                                {
                                    $rowval = $client_count;
                                }
                                
                                Tools::update_value(LapsController::getSlotPosCode($account_type_id), $rowval);
                                continue;
                            }
                            else
                            {
                                ($client_count >= 4) ? $pos1 = 1 : $pos1 = 0; 
                                ($client_count % 2 == 0) ? $pos2 = 0 : $pos2 = 1;
                                $client = Network::getClientNetwork($client_id, $lap_no, $pos1);
                                $laps->sponsor_id = $client[0]['client_id'];
                                $laps->pos = $pos2; 
                                break;
                                
                            }

                        } while ($client_count <= 5); //$client_count >=2 $client_count <= 5
                        
                    }
              
                }
                $laps->insertLap($lap_no);
                break;
        }
    }

    public static function getLapName($lap_no)
    {
        switch($lap_no)
        {
            case 1: $lap_name = 'lap_one'; break;
            case 2: $lap_name = 'lap_two'; break;
            case 3: $lap_name = 'lap_three'; break;
        }
        
        return $lap_name;
    }
    
    public static function getAvailableSlot($lap_no, $client_count, $account_type_id)
    {
        $model = new LapModel();
        $model->lap_no = LapsController::getLapName($lap_no);
        $model->account_type_id = $account_type_id;
        $result = $model->getLapNoInfo();
                
        $row_update = array();
        if(count($result)>0)
        {
            foreach($result as $row)
            {
                $account_id = $row['client_id'];
                $network = Network::getNetworkCount($account_id,3);
                $table_count = count($network);            

                if( $table_count == $client_count)
                {
                    $row_update[] = $account_id;
                }

            }
            
            //Table has an account with 2 downlines
            if(count($row_update) > 0)
            {
                return $row_update[0];
            }
            else
            {
                return false;
            }
            
        }
        else
        {
            return false;
        }
        
        
        
    }
    
    public function actionTable()
    {
        $this->initialize();
        
        $model = new RegistrationForm();
        $clients = new Clients();
        $lap_no = "";
        
        if(isset($_GET))
        {
            $clients->account_id = $_GET['id'];
            $lap_no = $_GET['lap_no'];
            
        }
                        
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];
        
        switch($client_info['account_type_id'])
        {
            case 5: $account_type = 'Jump Start';break;
            case 6: $account_type = 'Main Turbo';break;
            case 7: $account_type = 'VIP Nitro';break;
        }
                   
        $this->render('_table',array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'account_type'=>$account_type,
            'lap_no'=>$lap_no,
            'model'=>$model,
        ));
    }
    
    public static function getSlotPos($account_type_id)
    {
        switch($account_type_id)
        {
            case 5: return Tools::get_value('JS_SLOT_POS');
            case 6: return Tools::get_value('MT_SLOT_POS');
            case 7: return Tools::get_value('VN_SLOT_POS');
        }
    }
    
    public static function getSlotPosCode($account_type_id)
    {
        switch($account_type_id)
        {
            case 5: return 'JS_SLOT_POS';
            case 6: return 'MT_SLOT_POS';
            case 7: return 'VN_SLOT_POS';
        }
    }
    
    public static function getPackageName($account_type_id)
    {
        switch($account_type_id)
        {
            case 5: return 'Jump Start';
            case 6: return 'Main Turbo';
            case 7: return 'VIP Nitro';
        }
    }
    
    public static function getBaseID($account_type_id)
    {
        switch($account_type_id)
        {
            case 5: return 'JS_BASE_ACCOUNT_ID';
            case 6: return 'MT_BASE_ACCOUNT_ID';
            case 7: return 'VN_BASE_ACCOUNT_ID';
        }
    }
    
}


