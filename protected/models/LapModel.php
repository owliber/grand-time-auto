<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 21, 2015
 * @filename LapModel.php
 */

class LapModel extends CFormModel
{
    public $_conn;
    public $lap_no;
    public $account_id;
    public $new_account_id;
    public $sponsor_id;
    public $pos;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function getLapNoInfo()
    {
       
        $conn = $this->_conn;
        $sql = "SELECT * FROM $this->lap_no";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
        
    }
    
    public function getLapInfo()
    {
        $conn = $this->_conn;
        $sql = "SELECT max(lap_no) AS lap_no FROM lap_info 
                WHERE account_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function insertLap($lap_no)
    {
        switch($lap_no)
        {
            case 1: $lap_no = 'lap_one'; break;
            case 2: $lap_no = 'lap_two'; break;
            case 3: $lap_no = 'lap_three'; break;
        }
        
        $conn = $this->_conn;
        $sql = "INSERT INTO $lap_no (client_id, sponsor_id, pos)
                 VALUES (:client_id, :sponsor_id, :pos);";
        $command = $conn->createCommand($sql);
        $command->bindParam(':client_id', $this->new_account_id);
        $command->bindParam(':sponsor_id', $this->sponsor_id);
        $command->bindParam(':pos',$this->pos);
        $command->execute();
    }
    
    public function lapComplete($lap_no)
    {
               
        $conn = $this->_conn;
        $sql = "INSERT INTO lap_info (account_id, lap_no) 
                VALUES (:account_id, :lap_no)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->bindParam(':lap_no', $lap_no);
        $command->execute();
        
    }
    
    public function getBaseAccount()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM $this->lap_no WHERE sponsor_id IS NULL";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function getLapSlot()
    {
        switch($this->lap_no)
        {
            case 1: $lap_no = 'lap_one'; break;
            case 2: $lap_no = 'lap_two'; break;
            case 3: $lap_no = 'lap_three'; break;
        }
        
        $conn = $this->_conn;
        $sql = "SELECT
                t1.client_id,
                count(t2.sponsor_id) AS client_count
              FROM $lap_no t1
                LEFT JOIN (SELECT
                    *
                  FROM $lap_no lt) t2
                  ON t1.client_id = t2.sponsor_id
              WHERE t1.sponsor_id IS NOT NULL
              GROUP BY t2.sponsor_id
              HAVING COUNT(t2.sponsor_id) < 2
              ORDER BY t1.tree_id
              LIMIT 1;";
        $command = $conn->createCommand($sql);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getLapCompleted()
    {
        $conn = $this->_conn;
        if($this->lap_no == 2)
        {
            $sql = "SELECT
                    t1.*
                  FROM lap_two t1
                    LEFT OUTER JOIN lap_three t2
                      ON t1.client_id = t2.client_id
                  WHERE t2.client_id IS NULL;";
        }
        else
        {
            $sql = "SELECT
                    lt.*
                  FROM lap_three lt
                    LEFT OUTER JOIN lap_info li
                      ON lt.client_id = li.account_id
                      AND li.lap_no = 3
                  WHERE li.account_id IS NULL;";
        }
        
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
}


