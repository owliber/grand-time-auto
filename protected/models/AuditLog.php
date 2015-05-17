<?php

/**
 * @author owliber
 * @date Oct 2, 2012
 * @filename AuditLog.php
 * 
 */

class AuditLog extends CFormModel
{
    public $_connection;
    public $audit_function_id;
    public $details;
    public $status;
        
    public function __construct() {
        $this->_connection = Yii::app()->db;
    }
    
    public function log_event()
    {
        $conn = $this->_connection;
            
        $ip_address = $_SERVER['REMOTE_ADDR'];
                
        $user_id = Yii::app()->user->getUserId();
        
        $query = "INSERT INTO audit_logs (audit_function_id,account_id,details,ip_address,`status`)
                  VALUE (:audit_function_id,:account_id,:details,:ip_address,:status)";

        $sql = $conn->createCommand($query);  
        $sql->bindValues(array(
                    ":audit_function_id"=>$this->audit_function_id,
                    ":account_id"=>$user_id,
                    ":details"=>$this->details,
                    ":ip_address"=>$ip_address,
                    ":status"=>$this->status,
        ));
        $sql->execute();
       
    }
    
    public function getLogs()
    {
        $conn = $this->_connection;
        $sql = "SELECT
                    al.audit_log_id,
                    CASE al.audit_function_id
                        WHEN 1 THEN CONCAT(ra.audit_name, ' from user <b>', u.username,'</b> on ',al.date_created, ' using IP Address ',al.ip_address,'.')
                        WHEN 2 THEN CONCAT(ra.audit_name, ' from user <b>', u.username,'</b> on ',al.date_created,'.')
                        WHEN 3 THEN CONCAT('User <b>',u.username, '</b> has ',IF(al.status = 1,'successfully killed',' failed killing'), ' timekeeping DB|batch process ',al.details,' on ',al.date_created, ' using IP Address ',al.ip_address,'.')
                        WHEN 4 THEN CONCAT('User <b>',u.username, '</b> has ',IF(al.status = 1,'successfully killed',' failed killing'), ' payroll DB|batch process ',al.details,' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 5 THEN CONCAT('User <b>',u.username, '</b> has ',IF(al.status = 1,'successfully unposted',' failed unposting'), ' payroll DB|batch process ',al.details,' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 6 THEN CONCAT('User <b>',u.username, '</b> has ',IF(al.status = 1,'successfully deleted',' failed deleting'), ' payslips for DB|batch|count ',al.details,' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 9 THEN CONCAT('New account <b>',al.details, '</b> was created by <b>',u.username, '</b> ',IF(al.status = 1,' successfully',' has failed.'), ' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 10 THEN CONCAT('Account <b>',al.details, '</b> was enabled by <b>',u.username, '</b> ',IF(al.status = 1,' successfully',' but has failed.'), ' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 11 THEN CONCAT('Account <b>',al.details, '</b> was disabled by <b>',u.username, '</b> ',IF(al.status = 1,' successfully',' but has failed.'), ' on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 12 THEN CONCAT('Reset password was ',IF(al.status = 1,'successful','failed'), ' on ',al.date_created, ' using IP Address ',al.ip_address,' for email address ',al.details,'.')                       
                        WHEN 13 THEN CONCAT('Change password was ',IF(al.status = 1,'successful','failed'), ' for user <b>',al.details,'</b> on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 14 THEN CONCAT('Username <b>',al.details,'</b>',IF(al.status = 1,' was successfully unlocked ',' failed to unlock '), ' by <b>',u.username,'</b> on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 15 THEN CONCAT('<b>',u.username,'</b>  was denied from accessing <b>',al.details,'</b> on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 16 THEN CONCAT('<b>',u.username,'</b> has ',IF(al.status = 1,'successfully ','failed to'),' set the payroll batch no <b>',al.details,'</b> to viewable on ',al.date_created, ' using IP Address ',al.ip_address,'.')                       
                        WHEN 17 THEN CONCAT('<b>',u.username,'</b> has ',IF(al.status = 1,'successfully ','failed to'),' set the payroll batch no <b>',al.details,'</b> to hidden on ',al.date_created, ' using IP Address ',al.ip_address,'.')
                        WHEN 18 THEN CONCAT('<b>',u.username,'</b> has ',IF(al.status = 1,'successfully ','failed to'),' change the status of TK batch process # <b>',al.details,'</b> to completed on ',al.date_created, ' using IP Address ',al.ip_address,'.')
                    END audit_log
                  FROM audit_logs al
                    INNER JOIN ref_audit_functions ra
                      ON al.audit_function_id = ra.audit_function_id
                    LEFT JOIN users u
                      ON al.user_id = u.user_id
                  ORDER BY al.date_created DESC;";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
    
    public function getLastLogin($account_id)
    {
        $conn = $this->_connection;
        $sql =  "SELECT
                    t1.*
                  FROM audit_logs t1
                    LEFT JOIN (
                      SELECT
                        MAX(audit_log_id) AS log_id,
                        account_id
                      FROM audit_logs
                      WHERE audit_function_id = 1
                      AND account_id = :account_id
                    ) AS t2 ON t1.account_id = t2.account_id
                  WHERE t1.audit_log_id < t2.log_id
                    ORDER BY t1.audit_log_id desc LIMIT 1; ";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$account_id);
        $result = $command->queryRow();
        return $result;
    }
      
    public function log_cron()
    {
        $conn = $this->_connection;
        $query = "INSERT INTO cronlogs (job_id, log_message, status) 
                    VALUES (:job_id, :log_message, :status)";
        
        if(isset($this->status)) $this->status = 1;
        
        $sql = $conn->createCommand($query);
        $sql->bindValue(":job_id", $this->job_id);
        $sql->bindValue(":log_message", $this->log_message);
        $sql->bindValue(":status", $this->status);
        $sql->execute();
        
    }
    
}

