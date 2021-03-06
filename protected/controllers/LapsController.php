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
                   
        $this->render('_first',array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
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
                   
        $this->render($view,array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
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
                   
        $this->render($view,array(
            'clients'=>$clients,
            'client_info'=>$client_info,
            'model'=>$model,
        ));
        
    }
    
    public static function createLap($lap_no, $account_id)
    {
        $laps = new LapModel();
        $laps->new_account_id = $account_id;
        
        $clients = new Clients();
        $clients->account_id = $account_id;
        $info = $clients->getClientInfo();
        
        switch($lap_no)
        {
            case 1: 
                $laps->insertLap($lap_no);
                break;
            default:
                $lap_count = Network::getLapCount($lap_no);
                //Check if first account set sponsor_id and position to null

                if($lap_count == 0)
                {
                    $laps->sponsor_id = NULL;
                    $laps->pos = NULL;
                }
                elseif($lap_count == 1 || $lap_count == 2  )
                {
                    $laps->lap_no = LapsController::getLapName($lap_no);
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
                        $laps->lap_no = $lap_no;
                        $info = $laps->getLapSlot();
                        $laps->sponsor_id = $info['client_id'];
                        $info['client_count'] == 1 ? $pos = 1 : $pos = 0;
                        $laps->pos = $pos;
                    }
                    
                    if($lap_no == 3)
                    {
                        $laps->lap_no = $lap_no;
                        
                        $client_count = Tools::get_value('OPEN_SLOT_POS');
                        do {
                            $client_id = LapsController::getAvailableSlot($lap_no, $client_count);
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
                                Tools::update_value('OPEN_SLOT_POS', $rowval);
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
    
    public static function getAvailableSlot($lap_no, $client_count)
    {
        $model = new LapModel();
        $model->lap_no = LapsController::getLapName($lap_no);
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
    
}


