<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 18, 2015
 * @filename TempModel.php
 */

class TempModel extends CFormModel
{
    public $_conn;
    public $payout_id;
    public $payout_deduction_id;
    public $amount;
    public $description;
     
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function getPayouts()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                *
              FROM payouts
              WHERE lap_no = 1
              AND head_count = 6
              AND payout_id NOT IN (SELECT
                  pd.payout_id
                FROM payout_details pd
                WHERE pd.payout_deduction_id = 10) ";
        $command = $conn->createCommand($sql);
        $result = $command->queryAll();
        
        if(count($result)>0)
            return $result;
        else
            return false;
    }
    
    public function createDeduction()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "INSERT INTO payout_details (payout_id, payout_deduction_id, amount, description)
                VALUES (:payout_id, :deduction_id, :amount, :desc)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':payout_id', $this->payout_id);
        $command->bindParam(':deduction_id', $this->payout_deduction_id);
        $command->bindParam(':amount', $this->amount);
        $command->bindParam(':desc', $this->description);
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(16, 'Add deduction to payout id:'.$this->payout_id, 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(16, $ex->getMessage(), 2);
        }
    }
}


