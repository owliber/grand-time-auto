<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Aug 11, 2014
 * @filename AccountsController
 */
class AccountsController extends Controller {
    
    public $dialogOpen = false;
    public $dialogMessage;
    public $dialogTitle;
    
    public function actionIndex()
    {
        $this->layout = "column2";
        
        $this->initialize();
        
        $model = new AccountsModel();
        $result = $model->getAccountLists();
        
        $dataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>25,
                                ),
                            ));
        
        $this->render('index',array(
                'dataProvider'=>$dataProvider,
                'model'=>$model,
            ));
    }
    
    public function actionProfile()
    {
        $this->layout = "column2";
        $model = new ProfileForm();
        
        
        
        if(Yii::app()->user->isAdmin() || Yii::app()->user->isSalesManager())
        {
            if(isset($_GET['id']) && !empty($_GET['id']))
            {
                $model->account_id = $_GET['id'];
            }
            else
            {
                $model->account_id = Yii::app()->user->getUserId();
            }
        }
        else
        if(Yii::app()->user->isClient())
            {
                $model->account_id = Yii::app()->user->getUserId();
        }
        else
        {
            throw new CHttpException('403', 'Forbidden access.');
        }
        
        if(isset($_POST['ProfileForm']))
        {
            $model->attributes = $_POST['ProfileForm'];
            $model->updateProfile();
            
            if(!$model->hasErrors())
            {
                
                $this->dialogTitle = 'Update Profile';
                $this->dialogMessage = 'Update profile was successful!';
            }
            
            $this->dialogOpen = true;
        }
        
        $result = $model->getProfileInfo();
        
        foreach($result as $row)
        {
            $model->referrer_id = $row['referrer_id'];
            $model->referrer_name = $row['referrer_name'];
            $model->account_id = $row['account_id'];
            $model->account_code = $row['account_code'];
            $model->first_name = $row['first_name'];
            $model->last_name = $row['last_name'];
            $model->middle_name = $row['middle_name'];
            $model->mobile_no = $row['mobile_no'];
            $model->birthdate = $row['birthdate'];
            $model->email = $row['email'];
            $model->address1 = $row['address1'];
            $model->address2 = $row['address2'];
            $model->status = $row['status'];
        }
        
        $this->render('profile',array(
            'model'=>$model,
        ));
    }
    
    public function actionClients()
    {
        $this->layout = "column2";
        
        $this->initialize();
        
        $model = new AccountsModel();
        
        if(isset($_POST['AccountsModel']))
        {
            $model->attributes = $_POST['AccountsModel'];
            $result = $model->getClientByKey();
        }
        else
        {
            $result = $model->getClientLists();
        }
        
        $dataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>25,
                                ),
                            ));
        
        $this->render('clients',array(
                'dataProvider'=>$dataProvider,
                'model'=>$model,
            ));
    }
    
    public function actionFindClients()
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
                        'value'=>$row['account_code'],
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
    
    public function actionAddUser()
    {
        if(isset($_POST['AccountsModel']))
        {
            $model = new AccountsModel();
            $model->attributes = $_POST['AccountsModel'];
                    
            if($model->validate())
            {          
                $model->addUser();

                if(!$model->hasErrors())
                {
                    
                    $result_code = 0;
                    $result_msg = "New account creation was succesful!";
                    //Mailer::sendCredentials($params);
                }
                else
                {
                    $result_code = 1;
                    $result_msg = "New account creation has failed!";
                }
                echo CJSON::encode(array(
                    'result_code'=>$result_code, 
                    'result_msg'=>$result_msg
                ));
                
                Yii::app()->end();
            }
            
            $error = CActiveForm::validate($model);
                            if($error!='[]')
                                echo $error;
                            Yii::app()->end();
        }
    }
    
    public function actionForgotPassword()
    {
        $model = new ForgotPasswordModel();
        if(isset($_POST['ForgotPasswordModel']))
        {
            $model->attributes = $_POST['ForgotPasswordModel'];
            $model->key = sha1(uniqid());

            if($model->validate())
            {
                $model->reset();
                
                if(!$model->hasErrors())
                {
                    $accounts = new AccountsModel();
                    $info = $accounts->getAccountInfo($model->key);
                    $email = $info['email'];
                    $params = array('key'=>$model->key, 'email'=>$email);
                    Mailer::sendResetLink($params);
                    $this->dialogMessage = "We have sent an instruction to your registered email address, please check your inbox and your spam folders.";
                }
                else
                {
                    $this->dialogMessage = "An error occured while submitting your request, please contact DBA Team";
                }
                $this->dialogTitle = 'Forgot Password';                
                $this->dialogOpen = true;
            }
        }

        $this->render('forgot',array(
            'model'=>$model,
        ));
    }

    public function actionValidate()
    {
        if(isset($_GET['key']) && ctype_alnum($_GET['key']))
        {
            if(strlen($_GET['key']) == 40)
            {
                $model = new ResetPasswordModel();
                $model->hashkey = $_GET['key'];

                $result = $model->validateKey();
                
                if(count($result)>0)
                {
                    $email = $result['email'];
                    //Get user info and redirect to 
                    $url = Yii::app()->createUrl('/accounts/reset', array('key'=>$model->hashkey,'email'=>$email));
                    $this->redirect($url);
                }
                else
                {
                    
                    $this->redirect(Yii::app()->createUrl('/site/login'));
                }


                $this->render('forgot',array(
                    'model'=>$model,
                ));
            }
            else
            {
                $this->dialogOpen = true;
                $this->dialogTitle = "Error";
                $this->dialogMessage = "Oops! An error occured or the key is invalid. Please try again.";
            }

        }
        else
        {
            $this->redirect(Yii::app()->createUrl('/site/login'));
        }

    }

    public function actionReset()
    {
                        
        if(isset($_GET['key']) && isset($_GET['email']))
        {
            $model = new ResetPasswordModel();
            
            $model->hashkey = $_GET['key'];
            $model->email = $_GET['email'];
            
            $valid = $model->validateKey();
            
            if(!$valid)
            {
                $this->redirect('site/login');
                Yii::app()->end();
            }
            
            if(isset($_POST['ResetPasswordModel']))
            {
                
                $model->attributes = $_POST['ResetPasswordModel'];
                
                if($model->validate())
                {
                    
                    $model->resetPassword();
                    
                    if(!$model->hasErrors())
                    {
                        $this->dialogMessage = "Great! You have successfully changed your password.";
                        $this->dialogTitle = "Successful";
                    }
                    else
                    {
                        $this->dialogMessage = "Password change has failed. Please contact IT";
                        $this->dialogTitle = "Failed";
                    }
                }
                else
                {
                    $this->dialogMessage = "Unable to validate new passwords. Please contact IT. ";
                    $this->dialogTitle = "Validation Failed";
                }

                $this->dialogOpen = true;

            }
            
        }
        
        $this->render('reset',array(
                    'model'=>$model,
                ));

    }
    
    public function actionResetPassword()
    {
        $model = new ResetPasswordModel();
        if(isset($_POST['ResetPasswordModel']))
        {
            if($model->validate())
            {
                $model->attributes = $_POST['ResetPasswordModel'];
                
                $model->resetPassword();
                
                if(!$model->hasErrors())
                {
                    $this->dialogMessage = "Password was successfully changed.";
                    $this->dialogTitle = "Successful";
                }
                else
                {
                    $this->dialogMessage = "Password change has failed";
                    $this->dialogTitle = "Failed";
                }
            }
                        
            $this->dialogOpen = true;
            
        }
    }
    
    public function actionDisable()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $id = $_GET['id'];
            
            $model = new AccountsModel();
            $model->account_id = $id;
            $model->disableAccount();
            
            if(!$model->hasErrors())
            {
                $result_code = 0;
                $result_msg = "Account is successfully disabled.";
            }
            else
            {
                $result_code = 1;
                $result_msg = "A problem encountered while disabling the account.";
            }
            echo CJSON::encode(array(
                'result_code'=>$result_code,
                'result_msg'=>$result_msg,
            ));
        }
    }
    
    public function actionEnable()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $id = $_GET['id'];
            
            $model = new AccountsModel();
            $model->account_id = $id;
            $model->enableAccount();
            
            if(!$model->hasErrors())
            {
                $result_code = 0;
                $result_msg = "Account is successfully enabled.";
            }
            else
            {
                $result_code = 1;
                $result_msg = "A problem encountered while enabling the account.";
            }
            echo CJSON::encode(array(
                'result_code'=>$result_code,
                'result_msg'=>$result_msg,
            ));
        }
    }
    
    public function  actionChangePassword()
    {
        $model = new ChangePasswordModel();
        $model->account_id = Yii::app()->session['account_id'];
        $model->setScenario('changePwd');
        
        if(isset($_POST['ChangePasswordModel']))
        {
            $model->attributes = $_POST['ChangePasswordModel'];
            
            if($model->validate())
            {
                $model->updatePassword();
                
                if(!$model->hasErrors())
                {
                    $this->dialogTitle = 'Successful';
                    $this->dialogMessage = 'Your password was successfully changed.';
                }
                else
                {
                    $this->dialogTitle = 'Failed';
                    $this->dialogMessage = 'Password changed has failed.';
                }
                
                $this->dialogOpen = true;
            }
        }
        
        $this->render('changepassword',array(
            'model'=>$model,
        ));
    }
    
    public function actionActivate()
    {
        if(isset($_GET['code']) && !empty($_GET['code']) && isset($_GET['email']) && !empty($_GET['email']))
        {
            $model = new AccountsModel();
            $model->account_code = $_GET['code'];
            $model->email = $_GET['email'];
            
            //validate code and email address
            if($model->validateAccount())
            {
                //enable account
                $model->activateAccount();
                
                if(!$model->hasErrors())
                {
                    echo '<script>
                            alert("Congratulations! You have successfully activated your account. You will now be redirected to the login page.");
                            window.location.href = "../site/login";
                         </script>';
                }
                else
                {
                    echo '<script>
                            alert("A problem encountered while activating your account. Please contact GTA support.");
                            window.location.href = "../site/login";
                         </script>';
                }
            }
            else
            {
                 echo '<script>
                            alert("Ooops! Invalid code or email address.");
                            window.location.href = "../site/login";
                         </script>';
            }
        }
    }
    
    public function actionResend()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new AccountsModel();
            $params['account_code'] = $_GET['account_code'];
            $params['client_name'] = $_GET['first_name'];
            $temp_password = substr(md5(uniqid(rand(1,6))), 0, 4);
            $params['temp_password'] = $temp_password;
            $params['email_address'] = $_GET['email'];
            $model->account_id = $_GET['id'];
            $model->temp_password = $temp_password;
            $model->updatePassword();
            
            if(!$model->hasErrors())
            {
                Mailer::sendAccountInfo($params);
                Tools::log(13, $model->account_id.'|'.$params['account_code'], 1);
                echo CJSON::encode(array('result_msg'=>'Activation was successfully sent!'));
            }
            else
            {
                Tools::log(13, $model->account_id.'|'.$params['account_code'], 2);
                echo CJSON::encode(array('result_msg'=>'Unable to send activation. Please contact IT.'));
            }
            Yii::app()->end();
        }
    }
        
}
