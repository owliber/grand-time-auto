<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 15, 2015
 * @filename Clients.php
 */

class Clients extends CFormModel
{
    public $_conn;
    public $account_id;
    public $sponsor_id;
    public $account_type_id;
    public $lap_no;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function getClients($filter)
    {
        $conn = $this->_conn;        
        $filter = "%".$filter."%";
                
        $sql = "SELECT
                    a.account_id as client_id,
                    a.account_code,
                    CONCAT(COALESCE(UPPER(ad.last_name),' '), ' ', COALESCE(ad.first_name,' '), ' ', COALESCE(ad.middle_name,' ')) AS client
                  FROM accounts a
                    LEFT JOIN account_details ad ON a.account_id = ad.account_id
                  WHERE (ad.last_name LIKE :filter
                    OR ad.first_name LIKE :filter
                    OR ad.middle_name LIKE :filter
                    OR a.account_code LIKE :filter
                    OR a.username LIKE :filter)
                    AND a.account_type_id = :account_type
                  ORDER BY ad.last_name";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(':filter', $filter);
        $command->bindParam(':account_type', $this->account_type_id);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function getSponsors($filter)
    {
        $conn = $this->_conn;        
        $filter = "%".$filter."%";
                
        $sql = "SELECT
                    a.account_id as referrer_id,
                    CONCAT(COALESCE(UPPER(ad.last_name),' '), ' ', COALESCE(ad.first_name,' '), ' ', COALESCE(ad.middle_name,' ')) AS sponsor
                  FROM accounts a
                    LEFT JOIN account_details ad ON a.account_id = ad.account_id
                  WHERE (ad.last_name LIKE :filter
                    OR ad.first_name LIKE :filter
                    OR ad.middle_name LIKE :filter
                    OR a.account_code LIKE :filter
                    OR a.username LIKE :filter)
                    AND a.account_type_id = :account_type
                  ORDER BY ad.last_name";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(':filter', $filter);
        $command->bindParam(':account_type', $this->account_type_id);
        $result = $command->queryAll();        
        return $result;
    }
        
    public function getPayoutClients($filter)
    {
        $conn = $this->_conn;        
        $filter = "%".$filter."%";
                
        $sql = "SELECT
                    a.account_id as client_id,
                    a.account_code,
                    CONCAT(COALESCE(UPPER(ad.last_name),' '), ' ', COALESCE(ad.first_name,' '), ' ', COALESCE(ad.middle_name,' ')) AS client
                  FROM accounts a
                    INNER JOIN payouts p ON a.account_id = p.account_id
                    LEFT JOIN account_details ad ON a.account_id = ad.account_id
                  WHERE (ad.last_name LIKE :filter
                    OR ad.first_name LIKE :filter
                    OR ad.middle_name LIKE :filter
                    OR a.account_code LIKE :filter)
                    AND a.account_type_id IN (5,6,7)
                  ORDER BY 3";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(':filter', $filter);
        $result = $command->queryAll();        
        return $result;
    }
    
    public function getLapClients()
    {
        $conn = $this->_conn;
        
        switch($this->lap_no)
        {
            case 1:
                $lap_table = 'lap_one';
                break;
            case 2:
                $lap_table = 'lap_two';
                break;
            case 3:
                $lap_table = 'lap_three';
                break;
        }
        
        $sql = "SELECT
                ct.client_id,
                ct.sponsor_id,
                CONCAT(coalesce(ad.first_name,''),' ',coalesce(ad.last_name,'')) AS client_name,
                a.account_code,
                a.account_type_id,
                ct.pos
              FROM $lap_table ct
                INNER JOIN accounts a
                  ON ct.client_id = a.account_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
              WHERE ct.sponsor_id = :sponsor_id;";
        
        $command = $conn->createCommand($sql);
        $command->bindParam(':sponsor_id', $this->account_id);
        $result = $command->queryAll();
        return $result;  
    }
    
    public function clientDetails($accounts)
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    a.account_id,
                    a.sponsor_id,
                    a.account_code,
                    ad.last_name, ad.first_name, ad.middle_name,
                    a.date_created  
                  FROM accounts a
                  LEFT JOIN account_details ad ON a.account_id = ad.account_id
                  WHERE a.account_id IN ($accounts);
                  ;";
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getClientCount()
    {
        $conn = $this->_conn;
        $query = "SELECT count(*) as total 
                    FROM accounts
                  WHERE sponsor_id = :sponsor_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':sponsor_id', $this->sponsor_id);
        $result = $command->queryRow();
        return $result["total"];
    }
        
    public function getClientInfo()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM accounts a
                    LEFT JOIN account_details ad ON a.account_id = ad.account_id
                WHERE a.account_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getClientSponsor()
    {
        $conn = $this->_conn;
        $sql = "SELECT 
                    a.account_id as client_id,
                    a.account_code,
                    a.sponsor_id
                FROM accounts a
                WHERE a.account_id = :account_id
                 AND a.sponsor_id = :sponsor_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $command->bindParam(':sponsor_id',$this->sponsor_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getDownlines()
    {
        switch($this->lap_no)
        {
            case 1: $lap_no = 'lap_one'; break;
            case 2: $lap_no = 'lap_two'; break;
            case 3: $lap_no = 'lap_three'; break;
        }
        
        $conn = $this->_conn;
        
        $sql = "SELECT
                lo.client_id,
                a.account_code,
                lo.sponsor_id,
                a.referrer_id,
                lo.pos
              FROM $lap_no lo
                INNER JOIN accounts a ON lo.client_id = a.account_id
              WHERE lo.sponsor_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getClientListByDate()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    a.account_id,
                    a.sponsor_id,
                    a.referrer_id,
                    CASE a.account_type_id WHEN 5 THEN 'Jump Start' WHEN 6 THEN 'Main Turbo' WHEN 7 THEN 'VIP Nitro' END race_type,
                    a.account_code,
                    CONCAT(COALESCE(ad.last_name, ''), ' ', COALESCE(ad.first_name, '')) AS account_name,
                    a.date_created
                  FROM accounts a
                    INNER JOIN account_details ad
                      ON a.account_id = ad.account_id
                    LEFT OUTER JOIN payouts p
                      ON a.account_id = p.account_id
                    LEFT OUTER JOIN job_queues jq
                      ON a.account_id = jq.account_id
                  WHERE a.date_created >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                  AND p.account_id IS NULL
                  AND jq.account_id IS NULL;";
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
}



