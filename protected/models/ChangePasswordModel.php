<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Mar 5, 2015
 * @filename ChangePasswordModel.php
 */

class ChangePasswordModel extends CFormModel
{
    public $oldpassword;
    public $newpassword;
    public $confirmpassword;
    public $account_id;
    public $_connection;
    
    public function __construct() {
       $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_id,oldpassword,newpassword,confirmpassword','required'),
            array('oldpassword', 'findPasswords', 'on' => 'changePwd'),
            array('confirmpassword', 'compare', 'compareAttribute'=>'newpassword','message'=>'Passwod does not match'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'oldpassword'=>'Old Password',
            'newpassword'=>'New Password',
            'confirmpassword'=>'Confirm Password',
        );
    }
    
    //matching the old password with your existing password.
    public function findPasswords($attribute, $params)
    {
        $user = Accounts::model()->findByPk(Yii::app()->user->getUserId());
        if ($user->password != md5($this->oldpassword))
            $this->addError($attribute, 'Old password is incorrect.');
    }
    
    public function updatePassword()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $sql = "UPDATE accounts SET password = md5(:password) WHERE account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':password', $this->newpassword);
        $command->bindParam(':account_id', $this->account_id);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(3, Yii::app()->user->getId(), 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(3, $ex->getMessage(), 2);
        }
    }
}