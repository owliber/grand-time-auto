<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Mar 2, 2015
 * @filename ResetModel.php
 */

class ResetPasswordModel extends CFormModel
{
    public $hashkey;
    public $newpassword;
    public $confirmpassword;
    public $email;
    public $_connection;
    
    public function __construct() {
       $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('email,newpassword,confirmpassword,hashkey','required'),
            array('confirmpassword', 'compare', 'compareAttribute'=>'newpassword','message'=>'Passwod does not match'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'newpassword'=>'New Password',
            'confirmpassword'=>'Confirm Password',
        );
    }
    
    public function validateKey()
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM accounts a "
                . "INNER JOIN account_details ad ON a.account_id = ad.account_id "
                . "WHERE a.update_key = :hashkey AND a.update_password = 1";
        $command = $conn->createCommand($sql);
        $command->bindParam(':hashkey', $this->hashkey);
        $result = $command->queryAll();
        return $result;
    }
    
    public function resetPassword()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $sql = "UPDATE accounts a "
                . "INNER JOIN account_details ad ON a.account_id = ad.account_id "
                . "SET a.password = md5(:newpassword), a.update_key = null, a.update_password = 0 "
                . "WHERE a.update_key = :hashkey AND ad.email = :email";
        $command = $conn->createCommand($sql);
        $command->bindParam(':hashkey', $this->hashkey);
        $command->bindParam(':email', $this->email);
        $command->bindParam(':newpassword', $this->newpassword);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(4, $this->email, 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(4, $ex->getMessage(), 2);
        }
    }
}