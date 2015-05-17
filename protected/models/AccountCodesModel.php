<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 13, 2015
 * @filename AccountCodesModel.php
 */

class AccountCodesModel extends CFormModel
{
    public $_conn;
    public $code_batch_id;
    public $account_type_id;
    public $account_type_name;
    public $quantity;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_type_id,quantity','required'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'account_type_id'=>'Account Type',
            'quantity'=>'Quantity',
        );
    }
    
    public function selectCodesByBatch()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                account_code_id,
                account_code,
                code_batch_id,
                CASE `status` WHEN 0 THEN 'Available' WHEN 1 THEN 'Used' END `status`
              FROM account_codes
                WHERE code_batch_id = :code_batch_id
              ORDER BY account_code_id DESC;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':code_batch_id', $this->code_batch_id);
        $result= $command->queryAll();
        return $result;
    }
    
    public function selectAllBatches()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    acb.code_batch_id,
                    at.account_type_id,
                    at.account_type_name,
                    acb.date_generated,
                    CONCAT(COALESCE(ad.first_name,''), ' ', COALESCE(ad.last_name,'')) AS generated_by,
                    acb.quantity,
                    COALESCE(t3.total_used,0) as used,
                    (acb.quantity - COALESCE(t3.total_used,0)) AS available,
                    acb.generated_from_ip
                  FROM account_code_batches acb
                    INNER JOIN account_details ad
                      ON acb.generated_by = ad.account_id
                    INNER JOIN account_types at
                      ON acb.account_type_id = at.account_type_id
                    LEFT JOIN (SELECT
                        code_batch_id,
                        COUNT(*) AS total_used
                      FROM account_codes
                      WHERE status = 1
                      GROUP BY code_batch_id) t3
                      ON acb.code_batch_id = t3.code_batch_id;";
        $command = $conn->createCommand($sql);
        $result= $command->queryAll();
        return $result;
    }
    
    public function listAccountCodeTypes()
    {
         return CHtml::listData(AccountTypes::getAccountCodeTypes(), 'account_type_id', 'account_type_name');
    }
        
    public function generateBatch($prefix)
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO account_code_batches (account_type_id, quantity,generated_by, generated_from_ip)"
                . " VALUES (:account_type_id, :quantity, :generated_by, :generated_from)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_type_id', $this->account_type_id);
        $command->bindParam(':quantity', $this->quantity);
        $command->bindParam(':generated_by', Yii::app()->user->getUserId());
        $command->bindParam(':generated_from', $remote_ip);
        $command->execute();
        $batch_id = $conn->lastInsertID;
        try
        {
            if(!$this->hasErrors())
            {
                $codes = $this->generate($prefix, $batch_id);
                $account_codes = implode(",", $codes); 
                
                $sql2 = "INSERT INTO account_codes (account_code, code_batch_id) VALUES " . $account_codes;
                $command2 = $conn->createCommand($sql2);
                $command2->execute();
                
                if(!$this->hasErrors())
                {
                    $trx->commit();
                    Tools::log(5, $batch_id, 1);
                }
                else
                {
                    $trx->rollback();
                    Tools::log(5, $this->getErrors(), 2);
                }
                
            }
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(5, $ex->getMessage(), 2);
        }
    }
    
    public function generate($prefix,$batch_id)
    {
        //Generate codes
        $generated_codes = CodeGenerator::generateCode($prefix,10,$this->quantity);
        for($i = 0; $i < $this->quantity; $i++)
        {
            $codes[] = "('".$generated_codes[$i]."'," . $batch_id . ")";
        }
        
        return $codes;
    }
    
    public function getCode()
    {
        $conn = $this->_conn;
        $sql = "SELECT account_code FROM account_codes WHERE status = 0 and account_code LIKE 'JS%' LIMIT 1";
        $command = $conn->createCommand($sql);
        $result = $command->queryRow();
        return $result['account_code'];
    }
    
}



