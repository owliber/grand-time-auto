<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 14, 2015
 * @filename RegistrationController.php
 */

class RegistrationController extends Controller
{
    public $layout = 'column2';
    public $dialogTitle;
    public $dialogMessage;
    public $dialogOpen = false;
    
    public function actionIndex()
    {
        $this->initialize();
        
        $model = new RegistrationForm();
        $clients = new Clients();
        $reference = new ReferenceModel();
        $param = array('package_name'=>'','url'=>'');
        
        if(isset($_GET['atid']) && !empty($_GET['atid']))
        {
            $atid = $_GET['atid'];
            $valid_atid = array(5,6,7);
        }
        else
        {
            throw new CHttpException('Invalid Access');
        }
        
        if(isset($_POST['RegistrationForm']))
        {
            $model->attributes = $_POST['RegistrationForm'];
            $clients->account_id = $model->client_id;
            
        }
        else
        {
            if(isset($_GET['id']))
            {
                $account_id = $_GET['id'];
                $clients->account_id = $account_id;
            }
            else
            {
                
                if(in_array($atid, $valid_atid))
                {
                    $base_id = LapsController::getBaseID($atid);                    
                    $clients->account_id = $reference->get_variable_value($base_id);
                }
                else
                {
                    throw new CHttpException('Invalid request');
                }
                
            }
            
        }
                
        $param['package_name'] = LapsController::getPackageName($atid);
        $param['url'] = Yii::app()->createAbsoluteUrl('registration/index', array('atid'=>$atid));

        $model->account_id = $clients->account_id;
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];

        $this->render('index',array(
            'client_info'=>$client_info,
            'clients'=>$clients,
            'model'=>$model,
            'param'=>$param
        ));
    }
    
    public function actionJumpStart()
    {
        $this->initialize();
        
        $model = new RegistrationForm();
        $clients = new Clients();
        $reference = new ReferenceModel();
        
        if(isset($_POST['RegistrationForm']))
        {
            $model->attributes = $_POST['RegistrationForm'];
            $clients->account_id = $model->client_id;
        }
        else
        {
            if(isset($_GET['id']))
            {
                $clients->account_id = $_GET['id'];
            }
            else
            {
                $clients->account_id = $reference->get_variable_value('JS_BASE_ACCOUNT_ID');
            }
        }

        $model->account_id = $clients->account_id;
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];

        $this->render('jumpstart',array(
            'client_info'=>$client_info,
            'clients'=>$clients,
            'model'=>$model,
        ));
    }
    
    public function actionMain()
    {
        $this->initialize();
        
        $model = new RegistrationForm();
        $clients = new Clients();
        $reference = new ReferenceModel();
        
        if(isset($_POST['RegistrationForm']))
        {
            $model->attributes = $_POST['RegistrationForm'];
            $clients->account_id = $model->client_id;
        }
        else
        {
            if(isset($_GET['id']))
            {
                $clients->account_id = $_GET['id'];
            }
            else
            {
                $clients->account_id = $reference->get_variable_value('MT_BASE_ACCOUNT_ID');
            }
        }

        $model->account_id = $clients->account_id;
        $client_info = $clients->getClientInfo();
        $clients->account_type_id = $client_info['account_type_id'];

        $this->render('turbo',array(
            'client_info'=>$client_info,
            'clients'=>$clients,
            'model'=>$model,
        ));
    }
    
    public function actionVip()
    {
        $this->render('nitro');
    }
        
    public function actionGetInfo()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $account_id = $_GET['id'];
            $sponsor_id = $_GET['sid'];
            $account_type_id = $_GET['atid'];   
            $position = $_GET['pos'];  
            
            switch ($account_type_id)
            {
                case 5: $header = "<h4>Jump Start Registration</h4>"; break;
                case 6: $header = "<h4>Main Turbo Registration</h4>"; break;
                case 7: $header = "<h4>VIP Nitro Registration</h4>"; break;
            }
            
            echo CJSON::encode(array(
                'account_id'=>$account_id,
                'sponsor_id'=>$sponsor_id,
                'account_type_id'=>$account_type_id,
                'position'=>$position,
                'header'=>$header
            ));
            Yii::app()->end();
        }
    }
    
    public function actionRegister()
    {
        $model = new RegistrationForm();
                
        if(isset($_POST['RegistrationForm']))
        {
            $model->attributes = $_POST['RegistrationForm'];
            $temp_password = substr(md5(uniqid(rand(1,6))), 0, 4);
            $model->temp_password = $temp_password;
            
        }
        
        if($model->validate())
        {
            //Proceed to registration
            $result = $model->register();
            
            if(!$model->hasErrors())
            {
                $clients = new Clients();
                $clients->account_id = $model->new_account_id;
                $client_info = $clients->getClientInfo();
                $params['account_code'] = $client_info['account_code'];
                $params['email_address'] = $client_info['email'];
                $params['client_name'] = $client_info['first_name'];
                $params['temp_password'] = $model->temp_password;

                //Send account information to user
                Mailer::sendAccountInfo($params);
                
            }
            
            //Redirect to url
            $url = Yii::app()->createUrl('registration/index', array('id'=>$model->account_id,'atid'=>$model->account_type_id));

            echo CJSON::encode(array(
                'result_code'=>$result['result_code'],
                'result_msg'=>$result['result_msg'],
//                'redirect'=>$_POST['redirect'],
                'url'=>$url,
             ));
            
            Yii::app()->end();

        }
        else
        {
            $error = CActiveForm::validate($model);
                            if($error!='[]')
                                echo $error;
                            Yii::app()->end();
        }
                     
    }
        
    public function actionClients()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']) && isset($_GET['atid']))
        {
            $model = new Clients();
            $model->account_type_id = $_GET['atid'];
            $result = $model->getClients($_GET['term']);

            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['client_id'],
                        'value'=>$row['client'],
                        'label'=>$row['client'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
    
    public function actionSponsors()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']) && isset($_GET['atid']))
        {
            $model = new Clients();
            $model->account_type_id = $_GET['atid'];
            $result = $model->getSponsors($_GET['term']);

            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['referrer_id'],
                        'value'=>$row['sponsor'],
                        'label'=>$row['sponsor'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
}




