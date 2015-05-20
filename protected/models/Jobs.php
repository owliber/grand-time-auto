<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 4, 2015
 * @filename Jobs.php
 */

class Jobs extends CFormModel
{
    public $_conn;
    public $job_id;
    public $cron_id;
    public $account_id;
    public $client_id;
    public $table_count;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function insert_queue()
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO job_queues (account_id,client_id,table_count) VALUES (:account_id,:client_id,:table_count)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        $command->bindParam(':client_id',$this->client_id);
        $command->bindParam(':table_count',$this->table_count);
        $command->execute();
                
    }
    
    public function get_queues()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                jq.job_queue_id,
                jq.account_id,
                jq.client_id,
                jq.table_count,
                a.account_code,
                a.account_type_id,
                CONCAT(COALESCE(ad.first_name, ''), ' ', COALESCE(ad.last_name, '')) AS account_name,
                CONCAT(COALESCE(ad2.first_name, ''), ' ', COALESCE(ad2.last_name, '')) AS client_name
              FROM job_queues jq
                INNER JOIN accounts a
                  ON jq.account_id = a.account_id
                LEFT JOIN account_details ad
                  ON jq.account_id = ad.account_id
                LEFT OUTER JOIN account_details ad2
                  ON jq.client_id = ad2.account_id;";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function process_queues()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "INSERT INTO job_processed (account_id,client_id,table_count) "
                . "SELECT account_id, client_id, table_count FROM job_queues WHERE job_queue_id = :job_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':job_id',$this->job_id);
        $command->execute();
        $job_processed_id = $conn->lastInsertID;
        
        try
        {
            $this->delete_queues();
            $trx->commit();
            $this->log_job($job_processed_id, 1, null, 1);
        } 
        catch (Exception $ex)
        {    
            $trx->rollback();
            $this->log_job($this->job_id, 1, $ex->getMessage(), 2);
        }
    }
    
    public function delete_queues()
    {
        $conn = $this->_conn;
        $sql = "DELETE FROM job_queues WHERE job_queue_id = :job_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':job_id',$this->job_id);
        $command->execute();
    }
    
    public function log_job($id,$type,$remarks,$status)
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO job_logs (job_processed_id, job_type_id, remarks, status) "
                . "VALUES (:job_processed_id, :job_type_id, :remarks, :status)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':job_processed_id',$id);
        $command->bindParam(':job_type_id',$type);
        $command->bindParam(':remarks',$remarks);
        $command->bindParam(':status',$status);
        $command->execute();
    }
    
    public function update_job_schedule()
    {
        $conn = $this->_conn;
        $sql = "UPDATE job_schedule SET last_run = now() WHERE cron_job_id = :cron_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':cron_id',$this->cron_id);
        $command->execute();
    }
    
    public function get_last_run()
    {
        $conn = $this->_conn;
        $sql = "SELECT * FROM job_schedule WHERE cron_job_id = :cron_id";
        $command = $conn->createCommand($sql);
        $command->bindParam(':cron_id',$this->cron_id);
        $result = $command->queryRow();
        return $result['last_run'];
    }
}




