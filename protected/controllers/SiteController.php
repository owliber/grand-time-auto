<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
            $this->layout = 'column2';
            
            $model = new PayoutModel();
            
            if(Yii::app()->user->isClient())
            {
                $account_id = Yii::app()->user->getUserId();
                $model->account_id = $account_id;
                $total = $model->getPayoutTotalByClient();
                $view = 'client';
                
            }
            else
            {
                $total = $model->getPayoutTotal();
                $view = 'index';
            }
            
            $lastLogin = Tools::lastLog(Yii::app()->user->getUserId());
                        
            $this->render($view,array(
                'lastLogin'=>$lastLogin,
                'total'=>$total,
            ));
            
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
            if(isset(Yii::app()->session['account_id']) && isset(Yii::app()->session['account_type_id']))
            {
                $this->redirect(array("site/index"));
            }
            
            $model = new LoginForm();

            // if it is ajax validation request
            if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
            {
                    echo CActiveForm::validate($model);
                    Yii::app()->end();
            }

            // collect user input data
            if(isset($_POST['LoginForm']))
            {
                    $model->attributes=$_POST['LoginForm'];
                    // validate user input and redirect to the previous page if valid
                    if($model->validate() && $model->login())
                    //$this->redirect(Yii::app()->user->returnUrl);
                        $this->redirect (array('site/index'));
            }
            // display the login form
            $this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
            $logged = Yii::app()->user->getId();
            //Tools::log(2, $logged, 1);
            Yii::app()->user->logout();
            $this->redirect(Yii::app()->homeUrl);
	}
        
        public function actionInvalid()
        {
            $this->render('invalid');
        }
        
        public function actionMaintenance()
        {
            $this->render('_maintenance');
        }
}