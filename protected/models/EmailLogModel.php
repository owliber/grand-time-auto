<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 20, 2015
 * @filename EmailLogModel.php
 */

class EmailLogModel extends CFormModel
{
    public $_conn;
    public $email_recipient;
    public $email_subject;
    public $message;
        
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function insertEmails()
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO email_logs (email_recipient, email_subject, message)
                VALUES (:email_recipient, :email_subject, :message);";
        $command = $conn->createCommand($sql);
        $command->bindParam(':email_recipient',$this->email_recipient);
        $command->bindParam(':email_subject',$this->email_subject);
        $command->bindParam(':message',$this->message);
        $command->execute();
    }
}
