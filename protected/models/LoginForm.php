<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Remember me next time',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
                if($this->_identity===null)
                {
                        $this->_identity=new UserIdentity($this->username,$this->password);
                        $this->_identity->authenticate();
                }
                if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
                {
                        //$user = Accounts::model()->findByAttributes(array('username'=>$this->username));
                        $user = Accounts::model()->find('username=:username OR account_code=:account_code',array(':username'=>$this->username,':account_code'=>$this->username));
                        $duration=$this->rememberMe ? Yii::app()->params['sessionTimeOut'] : 0; 
                        Yii::app()->user->login($this->_identity,$duration);
                        Yii::app()->session['account_type_id'] = $user->account_type_id;
                        Yii::app()->session['account_id'] = $user->account_id;

                        //Log activity
                        Tools::log(1, $this->username, 1);
                        $user->lastLogin($user->account_id);

                        return true;
                }
                else
                {
                        //Log activity
                        Tools::log(1, $this->username, 2);
                        return false;
                }
	}
}
