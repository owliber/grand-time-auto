<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 28, 2015
 * @filename PayoutModel.php
 */

class PayoutModel extends CFormModel
{
    public $_conn;
    public $account_id;
    public $account_code;
    public $payout_id;
    public $lap_no;
    public $head_count;
    public $matrix_code;
    public $matrix_id;
    public $date_from;
    public $date_to;
    public $status;
    public $total_amount;
    public $total_deductions;
    public $deduction_details;
    public $search_key;
    public $is_visible;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('date_from, date_to,status','required'),
            array('account_id,account_code,search_key','safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'date_from'=>'From',
            'date_to'=>'To',
            'status'=>'Status',
        );
    }
    
    public function getPayoutMatrix()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM payout_matrix WHERE code = :code";
        $command = $conn->createCommand($sql);
        $command->bindParam(':code', $this->matrix_code);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getDeductions()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM payout_deductions WHERE payout_matrix_id = :matrix_id AND status = 1";
        $command = $conn->createCommand($sql);
        $command->bindParam(':matrix_id', $this->matrix_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function generatePayout()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "INSERT INTO payouts (account_id, lap_no, head_count, total_amount, total_deductions, net_pay, is_visible)
                VALUES (:account_id, :lap_no, :head_count, :total_amount, :total_deductions, :total_amount - :total_deductions, :is_visible)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->bindParam(':lap_no',$this->lap_no);
        $command->bindParam(':head_count',$this->head_count);
        $command->bindParam(':total_amount',$this->total_amount);
        $command->bindParam(':total_deductions',$this->total_deductions);
        $command->bindParam(':is_visible',$this->is_visible);
        $command->execute();
        $this->payout_id = $conn->lastInsertID;        
        $deductions = $this->deduction_details;
        
        try 
        {
            
            if(count($deductions) > 0)
            {
                
                foreach($deductions as $row)
                {
                    $deduction_id = $row['payout_deduction_id'];
                    $deduction_amount = $row['deduction_amount'];
                    $description = $row['description'];
                    $this->insertDeductions($this->payout_id, $deduction_id, $deduction_amount, $description);                    
                }
            }
            
            if($this->hasPayoutSummary())
            {
                $this->updateSummary();
            }
            else
            {
                $this->insertSummary();
            }
            
            $trx->commit();
            Tools::log(7, $this->payout_id, 1);
            
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(7, $ex->getMessage(), 2);
        }
    }
    
    public function hasPayoutSummary()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM payout_summary WHERE account_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $result = $command->queryAll();
        if(count($result)>0)
            return true;
        else
            return false;
    }
    
    public function insertSummary()
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO payout_summary (account_id, total_amount) "
                . "VALUES (:account_id, :total_amount)";
        $total_amount = $this->total_amount - $this->total_deductions;
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->bindParam(':total_amount', $total_amount);
        $command->execute();
    }
    
    public function updateSummary()
    {
        $conn = $this->_conn;
        $sql = "UPDATE payout_summary SET total_amount = total_amount + :total_amount "
                . "WHERE account_id = :account_id";
        $total_amount = $this->total_amount - $this->total_deductions;
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->bindParam(':total_amount', $total_amount);
        $command->execute();
    }
    
    public function insertDeductions($payout_id, $deduction_id, $deduction_amount, $description)
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO payout_details (payout_id, payout_deduction_id, amount, description)
                VALUES (:payout_id, :deduction_id, :deduction_amount, :description)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':payout_id', $payout_id);
        $command->bindParam(':deduction_id', $deduction_id);
        $command->bindParam(':deduction_amount', $deduction_amount);
        $command->bindParam(':description', $description);
        $command->execute();
    }
    
    public function getPayoutRequests()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                p.payout_id,
                a.account_id,
                p.lap_no,
                CONCAT(COALESCE(ad.first_name, ''), ' ', COALESCE(ad.last_name, '')) AS account_name,
                a.account_code,
                FORMAT(p.total_amount,2) AS total_amount,
                FORMAT(p.total_deductions,2) AS total_deductions,
                FORMAT(p.net_pay,2) AS net_pay,
                DATE_FORMAT(p.date_created,'%M %d, %Y') AS date_created,
                p.processed_by,
                p.date_processed,
                CASE(p.`status`) WHEN 0 THEN 'Pending' WHEN 1 THEN 'Requested' WHEN 2 THEN 'Processed' END status_name,
                p.`status` AS status
              FROM payouts p
                INNER JOIN accounts a
                  ON p.account_id = a.account_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
              WHERE p.status = 1
               ORDER BY p.date_created DESC";
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getPayoutByDate()
    {
        $conn = $this->_conn;
        $where = "";
        
        
        if(!empty($this->search_key))
        {
            $filter = "%".$this->search_key."%";
            $where = " AND (a.account_code LIKE :filter
                       OR ad.last_name LIKE :filter
                       OR ad.first_name LIKE :filter )";
        }
        
        $sql = "SELECT
                p.payout_id,
                a.account_id,
                p.lap_no,
                CONCAT(COALESCE(ad.first_name, ''), ' ', COALESCE(ad.last_name, '')) AS account_name,
                a.account_code,
                FORMAT(p.total_amount,2) AS total_amount,
                FORMAT(p.total_deductions,2) AS total_deductions,
                FORMAT(p.net_pay,2) AS net_pay,
                DATE_FORMAT(p.date_created,'%M %d, %Y') AS date_created,
                p.processed_by,
                p.date_processed,
                CASE(p.`status`) WHEN 0 THEN 'Pending' WHEN 1 THEN 'Requested' WHEN 2 THEN 'Processed' END status_name,
                p.`status` AS status
              FROM payouts p
                INNER JOIN accounts a
                  ON p.account_id = a.account_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
              WHERE p.date_created BETWEEN :date_from AND date_add(:date_to,INTERVAL 1 DAY)
                AND p.status = :status "
               . $where
               . " ORDER BY p.date_created DESC";
        $command = $conn->createCommand($sql);
        $command->bindParam(':date_from',$this->date_from);
        $command->bindParam(':date_to',$this->date_to);
        $command->bindParam(':status',$this->status);
        if(!empty($this->search_key))
            $command->bindParam(':filter',$filter);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getPayoutByClient()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                p.payout_id,
                a.account_id,
                p.lap_no,
                a.account_code,
                COALESCE(FORMAT(p.total_amount,2), 0.00) AS gross_pay,
                COALESCE(FORMAT(p.total_deductions,2), 0.00) AS deductions,
                COALESCE(FORMAT(p.net_pay,2), 0.00) AS net_pay,
                DATE_FORMAT(p.date_created,'%M %d, %Y') AS date_created,
                p.processed_by,
                p.date_processed,
                CASE(p.`status`) WHEN 0 THEN 'Pending' WHEN 1 THEN 'Requested' WHEN 2 THEN 'Processed' END status_name,
                p.`status` AS status
              FROM payouts p
                INNER JOIN accounts a
                  ON p.account_id = a.account_id
                LEFT JOIN account_details ad
                  ON a.account_id = ad.account_id
              WHERE p.account_id = :account_id
                AND p.is_visible = 1              
               ORDER BY p.date_created DESC";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function getPayoutTotalByClient()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    COALESCE(FORMAT(SUM(p.net_pay),2),0.00) AS total_net_pay,
                    COALESCE(FORMAT(SUM(IF(p.status=2,p.net_pay,0)),2), 0.00) AS total_received,
                    COALESCE(FORMAT(SUM(IF(p.status=0 OR p.status=1,p.net_pay,0)),2), 0.00) AS total_receivables
                FROM payouts p
                WHERE account_id = :account_id AND p.is_visible = 1";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getPayoutTotal()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    COALESCE(FORMAT(SUM(p.total_amount), 2), 0.00) AS total_payins,
                    COALESCE(FORMAT(SUM(p.total_deductions), 2), 0.00) AS total_deductions,
                    COALESCE(FORMAT(SUM(p.total_amount) - SUM(p.total_deductions),2), 0.00) AS total_net_pay,
                    COALESCE(FORMAT(SUM(IF(p.status=2,p.net_pay,0)),2), 0.00) AS total_processed,
                    COALESCE(FORMAT(SUM(IF(p.status=1,p.net_pay,0)),2), 0.00) AS total_requests,
                    COALESCE(FORMAT(SUM(IF(p.status=0 OR p.status=1,p.net_pay,0)),2), 0.00) AS total_unprocessed
                  FROM payouts p;";
        $command = $conn->createCommand($sql);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getPayoutTotalByDate()
    {
        $conn = $this->_conn;
        $where = "";
        if(!empty($this->search_key))
        {
            $filter = "%".$this->search_key."%";
            $where = " AND (a.account_code LIKE :filter
                       OR ad.last_name LIKE :filter
                       OR ad.first_name LIKE :filter )";
        }
        $sql = "SELECT
                    COALESCE(FORMAT(SUM(p.total_amount), 2), 0.00) AS total_payins,
                    COALESCE(FORMAT(SUM(p.total_deductions), 2), 0.00) AS total_deductions,
                    COALESCE(FORMAT(SUM(p.total_amount) - SUM(p.total_deductions),2), 0.00) AS total_net_pay,
                    COALESCE(FORMAT(SUM(IF(p.status=2,p.net_pay,0)),2), 0.00) AS total_processed,
                    COALESCE(FORMAT(SUM(IF(p.status=1,p.net_pay,0)),2), 0.00) AS total_requests,
                    COALESCE(FORMAT(SUM(IF(p.status=0 OR p.status=1,p.net_pay,0)),2), 0.00) AS total_unprocessed
                FROM payouts p
                    INNER JOIN accounts a
                    ON p.account_id = a.account_id
                  LEFT JOIN account_details ad
                    ON a.account_id = ad.account_id
                WHERE p.status = :status
                 AND (p.date_created BETWEEN :date_from AND date_add(:date_to,INTERVAL 1 DAY)) "
                . $where;
        $command = $conn->createCommand($sql);
        $command->bindParam(':date_from',$this->date_from);
        $command->bindParam(':date_to',$this->date_to);
        $command->bindParam(':status',$this->status);
        if(!empty($this->search_key)) 
            $command->bindParam(':filter',$filter);
        $result = $command->queryRow();
        return $result;
    }
    
    public function processPayout()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "UPDATE payouts
                SET status = 2, date_processed = now(), processed_by = :aid
                WHERE payout_id = :payout_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':payout_id',$this->payout_id);
        $command->bindParam(':aid', Yii::app()->user->getUserId());
        $command->execute();
        try
        {
            $trx->commit();
        } catch (Exception $ex) {
            $trx->rollback();
        }
    }
    
    public function getPayoutSummary()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    a.account_id,
                    a.account_code,
                    CONCAT(COALESCE(ad.first_name, ''), ' ', COALESCE(ad.last_name, '')) AS account_name,
                    FORMAT(p.total_amount,2) AS total_amount,
                    FORMAT(p.total_deductions,2) AS total_deductions,
                    FORMAT(p.net_pay,2)AS net_pay,
                    p.date_created
                FROM payouts p
                    INNER JOIN accounts a ON p.account_id = a.account_id
                    INNER JOIN account_details ad ON a.account_id = ad.account_id
                WHERE payout_id = :payout_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':payout_id',$this->payout_id);
        $result = $command->queryRow();
        return $result;
    }
    
    public function getPayoutDetails()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    payout_detail_id,
                    payout_id,
                    payout_deduction_id,
                    FORMAT(amount,2) as amount,
                    description,
                    date_created
                FROM payout_details pd
                WHERE pd.payout_id = :payout_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':payout_id',$this->payout_id);
        $result = $command->queryAll();
        return $result;
    }
    
    public function hasPayout()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    *
                  FROM payouts
                  WHERE account_id = :account_id
                  AND lap_no = :lap_no
                  AND head_count = :head_count;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $command->bindParam(':lap_no',$this->lap_no);
        $command->bindParam(':head_count',$this->head_count);
        $result = $command->queryAll();
        if(count($result)>0)
        {
            return true;
        }
        else
        {
            return false;
        }       
    }
    
    public function requestPayout()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "UPDATE payouts SET status = 1, date_requested = now() 
                WHERE account_id = :account_id
                AND status = 0;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id', $this->account_id);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(15, $this->account_id, 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(15, $ex->getMessage(), 1);
        }
            
    }
}



