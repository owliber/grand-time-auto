<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Sep 16, 2014
 * @filename Tools
 */

class Tools {
    
    public static function log($audit_function_id, $details, $status)
    {
        $model = new AuditLog();
        $model->audit_function_id = $audit_function_id;
        $model->details = $details;
        $model->status = $status;        
        $model->log_event();
    }
        
    public static function lastLog($account_id)
    {
        $model = new AuditLog();
        $result = $model->getLastLogin($account_id);
        return $result['date_created'];
    }
    
    public static function mailer_on()
    {
        $model = new ReferenceModel();
        $retval = $model->get_variable_value('MAILER');
        if($retval == 1)
            return true;
        else
            return false;
    }
    
    public static function job_enabled()
    {
        $model = new ReferenceModel();
        $retval = $model->get_variable_value('JOB_SCHEDULER');
        
        if($retval == 1)
            return true;
        else
            return false;
    }
    
    public static function get_template($id)
    {
        $model = new ReferenceModel();
        $retval = $model->get_message_template($id);
        return $retval;
    }
    
    public static function get_value($const)
    {
        $model = new ReferenceModel();
        $retval = $model->get_variable_value($const);
        return $retval;
    }
    
    public static function add_client_count($account_type_id, $lap_no)
    {
        $const = Tools::get_ref_code($account_type_id, $lap_no);
        $model = new ReferenceModel();
        $model->update_statistics($const);
    }
    
    public static function update_value($const,$val)
    {
        $model = new ReferenceModel();
        $model->update_value($const,$val);
    }
    
    public static function get_ref_code($account_type_id, $lap_no)
    {
        switch($account_type_id)
        {
            case 5: $code = 'JS'; break;
            case 6: $code = 'MT'; break;
            case 7: $code = 'VN'; break;
        }
        $ref_code = 'LAP'.$lap_no.'_'.$code.'_TOTAL';
        return $ref_code;
    }
    
}
