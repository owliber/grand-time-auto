<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 12, 2015
 * @filename AccountTypes.php
 */

class AccountTypes extends CFormModel 
{
    
    public $_conn;
    public $account_type_id;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function getAccountTypeName()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM account_types WHERE account_type_id = :account_type_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id', $this->account_type_id);
        $result = $command->queryRow();
        return $result['account_type_name'];
    }
    
    public function getAccountCodeTypes()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM account_types WHERE account_codes = 1";
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
    
}


