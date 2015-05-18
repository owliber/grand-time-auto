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
        $model = new RegistrationForm();
        
        $this->render('index',array(
            'model'=>$model,
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
        $this->render('turbo');
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
            echo CJSON::encode(array(
                'account_id'=>$account_id,
                'sponsor_id'=>$sponsor_id,
                'account_type_id'=>$account_type_id,
                'position'=>$position,
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
            //Proceed to registratiion
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
            echo CJSON::encode(array(
                  'result_code'=>$result['result_code'],
                  'result_msg'=>$result['result_msg'],
                  'redirect'=>$_POST['redirect'],
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
    
    /*
    public static function network($client_id,$pos)
    {
        $client = array();
        if(!isset($client[$client_id]['account_code']))
             {
                 
                 echo TbHtml::ajaxLink('Register', 
                    array('getsponsor'),
                    array('type' => 'GET',
                        'data' => array(
                            'id'=>$model->account_id,
                            'sid'=>$model->account_id,
                            'atid'=>$model->account_type_id,
                            'pos'=>$pos
                        ),
                        'dataType' => 'json',
                        'success' => 'function(data){
                            $("#RegistrationForm_account_id").val(data.account_id);
                            $("#RegistrationForm_sponsor_id").val(data.sponsor_id);
                            $("#RegistrationForm_account_type_id").val(data.account_type_id);
                            $("#regform-dialog").modal("show");
                        }',
                        'beforeSend' => 'function() {           
                            $("#ajax-loader").addClass("loading");
                         }',
                         'complete' => 'function() {
                           $("#ajax-loader").removeClass("loading");
                         }', 
                    ),
                    array('id'=>  uniqid(),
                            'rel'=>'tooltip',
                            'data-original-title'=>'Click to register'
                    ));
             }
             else
             {
                 echo TbHtml::link($client[$client_id]['account_code'], 
                        array('jumpstart',
                            'id'=> $client[$client_id]['client'] 
                    ), array('rel'=>'tooltip',
                             'data-original-title'=>'Click to view the acounts downlines',
                    ));
                 
             }
    }
     * 
     */
    
    public function actionClients()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new Clients();
            $model->account_type_id = 5;
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
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new Clients();
            $model->account_type_id = 5;
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




