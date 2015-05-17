<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Mar 2, 2015
 * @filename ForgotPasswordModel.php
 */

class ForgotPasswordModel extends CFormModel
{
    public $_connection;
    public $account_code;
    public $key;
    
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_code','required'),
            array('account_code','verifyLoginCode'),
        );
    }
    
    public function attributeLabels() {
        return array(
          'account_code'=>'Username|Account Code', 
        );
    }
    
    public function verifyEmail()
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM account_details WHERE email = :email";
        $command = $conn->createCommand($sql);
        $command->bindParam(':email', $this->email);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function verifyLoginCode($attribute, $params)
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM accounts WHERE account_code = :account_code OR username = :account_code";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code', $this->account_code);
        $result = $command->queryAll();
        if(count($result) == 0)
           $this->addError($attribute, 'The entered code/username is incorrect or not valid.');
    }
    
    public function reset()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();                
        $sql = "UPDATE accounts a
                SET a.update_password = 1, a.update_key = :key
                WHERE a.username = :account_code OR a.account_code = :account_code";
        $command = $conn->createCommand($sql);
        $command->bindParam(':key',$this->key);
        $command->bindParam(':account_code',$this->account_code);
        $command->execute();      
        
        try
        {
            $trx->commit();
            Tools::log(14, $this->account_code, 1);
            
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(14, $ex->getMessage(), 2);
        }
    }
    
}