<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 12, 2015
 * @filename AccountModel
 */
class Accounts extends CActiveRecord {
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'accounts';
    }
    
    //perform one-way encryption on the password before we store it in the database
    protected function afterValidate()
    {
        parent::afterValidate();
        $this->Password = $this->hashPassword($this->Password);
    }
        
    public function hashPassword($value)
    {
        return md5($value);
    }
    
    public function lastLogin($id)
    {
        $conn = Yii::app()->db;
        $sql = "UPDATE accounts SET date_last_login = now() WHERE account_id = :account_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $id);
        $command->execute();
    }
}
