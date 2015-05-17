<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Feb 18, 2015
 * @filename ReferenceModel.php
 */

class ReferenceModel extends CFormModel
{
    public $_connection;
    public $current_date;
            
    public function __construct() {
        $this->_connection = Yii::app()->db;
        $this->current_date = date('Y-m-d');
    }
    
    public function get_variable_value($param)
    {
        $conn = $this->_connection;
        $query = "SELECT variable_value FROM ref_variables WHERE variable_name = :param";
        $command = $conn->createCommand($query);
        $command->bindParam(':param', $param);
        $result = $command->queryRow();
        return $result['variable_value'];
    }
    
    public function get_message_template($template_id)
    {
        $conn = $this->_connection;
        $query = "SELECT * FROM ref_message_template WHERE message_template_id = :template_id";
        $command = $conn->createCommand($query);
        $command->bindParam(':template_id', $template_id);
        $result = $command->queryRow();
        return $result['message_template'];
    }
       
    public function toggle_job_scheduler($status)
    {
        $conn = $this->_connection;
        $trx = $conn->beginTransaction();
        $query = "UPDATE ref_variables SET variable_value = :status
                  WHERE variable_name = 'JOB_SCHEDULER';";
        $command = $conn->createCommand($query);
        $command->bindParam(':status', $status);
        $command->execute();
        try
        {
            $trx->commit();
        }
        catch(PDOException $e)
        {
            $trx->rollback();
        }
    }
    
    public function email_recipients()
    {
        $conn = $this->_connection;
        $query = "SELECT email_address FROM email_recipients WHERE status = 1";
        $command = $conn->createCommand($query);
        $result = $command->queryAll();
        return $result;
    }
    
    public function update_statistics($variable)
    {
        $conn = $this->_connection;
        $sql = "UPDATE ref_variables SET variable_value = variable_value + 1
                WHERE variable_name = :variable_name; ";
        $command = $conn->createCommand($sql);
        $command->bindParam(':variable_name', $variable);
        $command->execute();
    }
    
    public function update_value($variable,$value)
    {
        $conn = $this->_connection;
        $sql = "UPDATE ref_variables SET variable_value = :variable_value
                WHERE variable_name = :variable_name; ";
        $command = $conn->createCommand($sql);
        $command->bindParam(':variable_name', $variable);
        $command->bindParam(':variable_value', $value);
        $command->execute();
    }
}
