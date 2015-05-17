<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Aug 11, 2014
 * @filename AccountsModel
 */
class AccountsModel extends CFormModel {
    
    public $_connection;
    public $account_id;
    public $account_code;
    public $email;
    public $account_type_id;
    public $first_name;
    public $last_name;
    public $username;
    public $password;
    public $repeat_password;
    public $search_key;
    public $temp_password;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('first_name,last_name,email,account_type_id,username,password,repeat_password','required'),
            array('email','email'),
            array('repeat_password', 'compare', 'compareAttribute'=>'password','message'=>'Passwod does not match'),
            array('username', 'checkUsername'),
            array('account_id,account_code,search_key','safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'first_name'=>'First Name',
            'last_name'=>'Last Name',
            'account_type_id'=>'Account Type',
            'email'=>'Email',
            'username'=>'Username',
            'password'=>'Password',
            
        );
    }
    
    public function checkUsername($attribute, $params)
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM accounts WHERE username = :username";
        $command = $conn->createCommand($sql);
        $command->bindParam(':username',$this->username);
        $result = $command->queryAll();
        if(count($result)>0)
            $this->addError($attribute, 'Username is already in use.');
        
                
    }
    
    public function getAccountLists()
    {
        $conn = $this->_connection;
        $sql = "SELECT
                a.account_id,
                a.username,
                date_last_login,
                CONCAT(coalesce(ad.first_name,''),' ',coalesce(ad.last_name,'')) AS `account`,
                rat.account_type_name,
                CASE a.status when 0 THEN 'Pending' when 1 THEN 'Active' WHEN 2 THEN 'Inactive' END `status`
              FROM accounts a
                INNER JOIN account_types rat
                  ON a.account_type_id = rat.account_type_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
                LEFT JOIN account_group_members agp ON a.account_type_id = agp.account_type_id
                INNER JOIN account_groups ag ON agp.account_group_id = ag.account_group_id
                WHERE ag.account_group_id = 1";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function getClientLists()
    {
        $conn = $this->_connection;
        $sql = "SELECT
                a.account_id,
                a.username,
                a.account_code,
                date_last_login,
                CONCAT(coalesce(ad.first_name,''),' ',coalesce(ad.last_name,'')) AS `client_name`,
                rat.account_type_name,
                CASE a.status when 0 THEN 'Pending' when 1 THEN 'Active' WHEN 0 THEN 'Inactive' END `status`
              FROM accounts a
                INNER JOIN account_types rat
                  ON a.account_type_id = rat.account_type_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
                LEFT JOIN account_group_members agp ON a.account_type_id = agp.account_type_id
                INNER JOIN account_groups ag ON agp.account_group_id = ag.account_group_id
                WHERE ag.account_group_id = 2";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function getClientByKey()
    {
        $conn = $this->_connection;
        $filter = "%".$this->search_key."%";
        $sql = "SELECT
                a.account_id,
                a.username,
                a.account_code,
                date_last_login,
                CONCAT(coalesce(ad.first_name,''),' ',coalesce(ad.last_name,'')) AS `client_name`,
                rat.account_type_name,
                CASE a.status when 0 THEN 'Pending' when 1 THEN 'Active' WHEN 0 THEN 'Inactive' END `status`
              FROM accounts a
                INNER JOIN account_types rat
                  ON a.account_type_id = rat.account_type_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
                WHERE (ad.last_name LIKE :filter
                    OR ad.first_name LIKE :filter
                    OR ad.middle_name LIKE :filter
                    OR a.account_code LIKE :filter
                    OR a.username LIKE :filter)
                    AND a.account_type_id = :account_type_id
                  ORDER BY 3";
        $command = $conn->createCommand($sql);
        $command->bindParam(':filter', $filter);
        $command->bindParam(':account_type_id', $this->account_type_id);
        return $command->queryAll();
    }
    
    public function getAccountTypes()
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM account_types WHERE account_type_id IN (2,3,4) AND status = 1";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function listAccountTypes()
    {
        return CHtml::listData(AccountsModel::getAccountTypes(), 'account_type_id', 'account_type_name');
    }
    
    public function getClientTypes()
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM account_types WHERE account_type_id IN (5,6,7) AND status = 1";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function listClientTypes()
    {
        return CHtml::listData(AccountsModel::getClientTypes(), 'account_type_id', 'account_type_name');
    }
    
    public function addUser()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $sql = "INSERT INTO accounts (account_type_id, username, password,status)
                VALUES (:account_type_id, :username, md5(:password),1)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id',$this->account_type_id);
        $command->bindParam(':username',$this->username);
        $command->bindParam(':password',$this->password);
        $command->execute();
        $this->account_id = $conn->lastInsertID;
        
        try
        {
            $this->insertDetails();
        
            if(!$this->hasErrors())
            {
                $trx->commit();
                Tools::log(8, $this->account_id .'|'.$this->username, 1);
            }
            else
            {
                $trx->rollback();
                Tools::log(8, $this->account_id .'|'.$this->username, 2);
            }
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(8, $ex->getMessage(), 2);
        }
        
    }
    
    public function insertDetails()
    {
        $conn = $this->_connection;
        $sql = "INSERT INTO account_details (account_id, last_name, first_name, email)
                    VALUES (:account_id, :lastname, :firstname, :email)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $command->bindParam(':lastname',$this->last_name);
        $command->bindParam(':firstname',$this->first_name);
        $command->bindParam(':email',$this->email);
        $command->execute();
    }
    
    public function checkEmail()
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
    
    public function disableAccount()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $sql = "UPDATE accounts SET status = 2 WHERE account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(10, $this->getUsernameByID(), 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(10, $ex->getMessage(), 2);
        }
    }
    
    public function enableAccount()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        
        $sql = "UPDATE accounts SET status = 1 WHERE account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(9, $this->getUsernameByID(), 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(9, $ex->getMessage(), 2);
        }
    }
    
    public function getUsernameByID()
    {
        $conn = $this->_connection;
        $sql = "SELECT username FROM accounts u WHERE u.account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $result = $command->queryRow();
        return $result['username'];
    }
    
    public function validateAccount()
    {
        $conn = $this->_connection;
        $sql = "SELECT
            *
          FROM accounts a
            LEFT JOIN account_details ad
              ON a.account_id = ad.account_id
          WHERE a.account_code = :account_code
          AND ad.email = :email;
          ";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code',$this->account_code);
        $command->bindParam(':email', $this->email);
        $result = $command->queryAll();
        if(count($result) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function activateAccount()
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $sql = "UPDATE accounts a
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
                SET a.STATUS = 1
                WHERE a.account_code = :account_code
                AND ad.email = :email;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code',$this->account_code);
        $command->bindParam(':email', $this->email);
        $command->execute();
        
        if(!$this->hasErrors())
        {
            $trx->commit();
            Tools::log(11, $this->account_code, 1);
        }
        else
        {
            $trx->rollback();
            Tools::log(11, $ex->getMessage(), 2);
        }
    }
    
    public function updatePassword()
    {
        $conn = $this->_connection;
        $sql = "UPDATE accounts SET password = md5(:temp_password) "
                . "WHERE account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':temp_password',$this->temp_password);
        $command->bindParam(':account_id',$this->account_id);
        $command->execute();
    }
    
    public function getAccountInfo($key)
    {
        $conn = $this->_connection;
        $sql = "SELECT * FROM accounts a 
                INNER JOIN account_details ad ON a.account_id = ad.account_id
                WHERE a.update_key = :hash_key;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':hash_key',$key);
        $result = $command->queryRow();
        return $result;
    }
}
