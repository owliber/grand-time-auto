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
    public $lap_table;
    public $account_id;
    public $account_code;
    public $account_type_id;    
    public $new_account_id;
    public $sponsor_id;
    public $pos;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_code,account_type_id,lap_no','required'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'account_code'=>'Name or Account Code',
        );
    }
    
    public function getLapNoInfo()
    {
       
        $conn = $this->_conn;
        $sql = "SELECT * FROM $this->lap_no l
                INNER JOIN accounts a ON l.client_id = a.account_id
              WHERE a.account_type_id = :account_type_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id', $this->account_type_id);
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
        $lap_table = LapsController::getLapName($lap_no);
        $conn = $this->_conn;
        $sql = "INSERT INTO $lap_table (client_id, sponsor_id, pos)
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
        $sql = "SELECT
                *
              FROM lap_one l
                INNER JOIN accounts a
                  ON l.client_id = a.account_id
              WHERE l.sponsor_id IS NULL
              AND a.account_type_id = :account_type_id;";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function getLapSlot()
    {       
        $conn = $this->_conn;
        $sql = "SELECT
                t1.client_id,
                COUNT(t2.sponsor_id) AS client_count
              FROM lap_two t1
                INNER JOIN accounts a ON t1.client_id = a.account_id
                LEFT JOIN (SELECT
                    *
                  FROM lap_two lt) t2
                  ON t1.client_id = t2.sponsor_id
              WHERE t1.sponsor_id IS NOT NULL
                AND a.account_type_id = :account_type_id
              GROUP BY t2.sponsor_id
              HAVING COUNT(t2.sponsor_id) < 2
              ORDER BY t1.tree_id
              LIMIT 1;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id', $this->account_type_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getLapCompleted()
    {
        $conn = $this->_conn;
        if($this->lap_no == 2)
        {
            $sql = "SELECT
                        a.account_type_id,
                        t1.*
                      FROM lap_two t1
                        LEFT JOIN accounts a ON t1.client_id = a.account_id
                        LEFT OUTER JOIN lap_three t2
                          ON t1.client_id = t2.client_id
                      WHERE t2.client_id IS NULL;";
        }
        else
        {
            $sql = "SELECT
                    a.account_type_id,
                    lt.*
                  FROM lap_three lt
                    LEFT JOIN accounts a ON lt.client_id = a.account_id
                    LEFT OUTER JOIN lap_info li
                      ON lt.client_id = li.account_id
                      AND li.lap_no = 3
                  WHERE li.account_id IS NULL;";
        }
        
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getLapResults()
    {
        $conn = $this->_conn;
        $filter = '%'.$this->account_code.'%';
        $sql = "SELECT
                    a.account_id,
                    a.account_code,
                    CONCAT(UPPER(ad.last_name), ', ', ad.first_name) AS account_name,
                    CASE a.account_type_id WHEN 5 THEN 'Jump Start' WHEN 6 THEN 'Main Turbo' WHEN 7 THEN 'VIP Nitro' END AS race_type,
                    :lap_no AS lap_no
                  FROM accounts a
                    INNER JOIN account_details ad
                      ON a.account_id = ad.account_id
                    INNER JOIN $this->lap_table t ON a.account_id = t.client_id
                  WHERE a.account_type_id = :account_type_id
                  AND (ad.last_name LIKE :filter
                  OR ad.first_name LIKE :filter
                  OR a.account_code LIKE :filter
                  );";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id', $this->account_type_id);
        $command->bindParam(':lap_no', $this->lap_no);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();
        return $result;
        
    }
    
    public function getAllLapResults()
    {
        $conn = $this->_conn;
        $filter = '%'.$this->account_code.'%';
        
        if($this->account_type_id == 'all')
        {
            $this->account_type_id = '5,6,7';
        }
        
        switch($this->lap_no)
        {
            case 'all': 
                $lap_no = 0;
                $LEFT_JOIN = " LEFT JOIN (
                            SELECT
                              client_id,
                              1 AS lap_no
                            FROM lap_one
                          ) AS lap1 ON a.account_id = lap1.client_id
                          LEFT JOIN (
                            SELECT
                              client_id,
                              2 AS lap_no
                            FROM lap_two
                          ) AS lap2 ON a.account_id = lap2.client_id
                          LEFT JOIN (
                            SELECT
                              client_id,
                              3 AS lap_no
                            FROM lap_one
                          ) AS lap3 ON a.account_id = lap3.client_id ";
                          
                break;
            case 1: 
                $lap_no = 1;
                $LEFT_JOIN = " LEFT JOIN (
                            SELECT
                              client_id,
                              1 AS lap_no
                            FROM lap_one
                          ) AS lap1 ON a.account_id = lap1.client_id ";
                break;
            case 2: 
                $lap_no = 2;
                $LEFT_JOIN = " LEFT JOIN (
                            SELECT
                              client_id,
                              2 AS lap_no
                            FROM lap_two
                          ) AS lap2 ON a.account_id = lap2.client_id ";
                break;
            case 3: 
                $lap_no = 3;
                $LEFT_JOIN = " LEFT JOIN (
                            SELECT
                              client_id,
                              3 AS lap_no
                            FROM lap_three
                          ) AS lap3 ON a.account_id = lap3.client_id ";
                break;
                
        }
        
        $sql = "SELECT
                a.account_id,
                a.account_code,
                CONCAT(UPPER(ad.last_name), ', ', ad.first_name) AS account_name,
                CASE a.account_type_id WHEN 5 THEN 'Jump Start' WHEN 6 THEN 'Main Turbo' WHEN 7 THEN 'VIP Nitro' END AS race_type,
                $lap_no AS lap_no
              FROM accounts a
                INNER JOIN account_details ad
                  ON a.account_id = ad.account_id "
                . $LEFT_JOIN
                . " WHERE a.account_type_id IN ($this->account_type_id)
                AND (a.account_code LIKE :filter
                OR ad.last_name LIKE :filter
                OR ad.first_name LIKE :filter)
                ;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();
        return $result;
    }
}


