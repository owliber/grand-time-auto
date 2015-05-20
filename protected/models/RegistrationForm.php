<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 14, 2015
 * @filename RegistrationForm.php
 */

class RegistrationForm extends CFormModel
{
    
    public $_conn;
    public $account_id;
    public $account_name;
    public $client_id;
    public $new_account_id;
    public $referrer_id;
    public $referrer_name;
    public $first_name;
    public $last_name;
    public $sponsor_id;
    public $account_code;
    public $account_type_id;
    public $email;
    public $temp_password;
    public $pos;
        
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_id,referrer_id,first_name,last_name,account_code,email,account_type_id,pos','required'),
            array('account_name,sponsor_id,temp_password,client_id','safe'),
            array('email','email'),
            array('account_code','length','is'=>12),
            array('account_code','checkCode'),
            
        );
    }
    
    public function attributeLabels() {
        return array(
            'first_name'=>'First Name',
            'last_name'=>'Last Name',
            'account_code'=>'Account Code',
            'email'=>'Email Address',
            'referrer_name'=>'Sponsor', //synonymous to referrer
            'account_type_id'=>'Account Type',
        );
    }
    
    public function checkCode($attribute, $params)
    {
        if(!$this->validateCode())
        {
            $this->addError($attribute, 'Account code is either used or invalid.');
            return false;
        }
    }
       
    public function validateCode()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                ac.account_code
                FROM account_codes ac
                  INNER JOIN account_code_batches acb
                    ON ac.code_batch_id = acb.code_batch_id
                WHERE ac.account_code = :account_code
                AND acb.account_type_id = :account_type
                AND ac.status = 0;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code', $this->account_code);
        $command->bindParam(':account_type', $this->account_type_id);
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
    
    public function register()
    {
        $conn = $this->_conn;
        $model = new LapModel();
        $job = new Jobs();
        
        $trx = $conn->beginTransaction();
        $sql = "INSERT INTO accounts (account_code,account_type_id,sponsor_id,referrer_id, password, created_by) "
                . "VALUES (:account_code,:account_type_id,:sponsor_id, :referrer_id, md5(:password), :createdby);";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code', $this->account_code);
        $command->bindParam(':account_type_id', $this->account_type_id);
        $command->bindParam(':sponsor_id', $this->sponsor_id);
        $command->bindParam(':referrer_id', $this->referrer_id);
        $command->bindParam(':password', $this->temp_password);
        $command->bindParam(':createdby', Yii::app()->user->getUserId());
        $command->execute();
        
        /* Insert and  update client details */
        $this->new_account_id = $conn->lastInsertID;
        $this->insertDetails();
        $this->updateCode();
        
        /* Create new lap record */
        $model->new_account_id = $this->new_account_id;
        $model->sponsor_id = $this->sponsor_id;
        $model->pos = $this->pos;
        $model->insertLap(1);
        
        /* Validate the number of clients per account */
        $client_count = Network::getClientCount($this->sponsor_id);

        //Get total table count
        $network = Network::getNetworkCount($this->account_id,1);
        $table_count = count($network);
        
        //Get reference records
        $ref_table_count = Tools::get_value('TOTAL_TABLE_CLIENT_PER_ACCOUNT');
        $ref_client_count = Tools::get_value('CLIENT_PER_ACCOUNT');
        $ref_min_client = Tools::get_value('MIN_CLIENT_FOR_PAYOUT');
        
        /* Add account on the job queues */
        $job->account_id = $this->account_id;
        $job->client_id = $this->new_account_id;
        $job->table_count = $table_count;
        
        try
        {
            if($table_count <= $ref_table_count && $client_count <= $ref_client_count)
            {
                switch($table_count)
                {
                    case $ref_table_count:
                    case $ref_min_client:
                        $job->insert_queue();
                        break;
                }
                
                $trx->commit();
                //Increment total client for lap 1 network
                Tools::add_client_count($this->account_type_id, 1);
                Tools::log(6, $this->account_code, 1);                                
                return array(
                    'result_code'=>0,
                    'result_msg'=>'You have successfully registered the account.',
                );
            }
            else
            {
                $trx->rollback();
                Tools::log(6, $this->account_code, 2);
                
                if($table_count == $ref_table_count)
                {
                    $result_code = 1;
                    $result_msg = 'The maximum slot for this table is already full.';
                }
                
                if($client_count == $ref_client_count)
                {
                    $result_code = 2;
                    $result_msg = 'This account has already (2) registered clients.';
                }
                                
                return array(
                    'result_code'=>$result_code,
                    'result_msg'=>$result_msg,
                );
            }
            
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(6, $ex->getMessage(), 2);
            
            return array(
                'result_code'=>3,
                'result_msg'=>'A problem was encountered while processing. Please contact IT.',
            );
        }
        
    }
    
    public function insertDetails()
    {
        $conn = $this->_conn;
        $sql = "INSERT INTO account_details (account_id, email, first_name, last_name) "
                    . "VALUES (:account_id,:email,:fname, :lname)";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->new_account_id);
        $command->bindParam(':email', $this->email);
        $command->bindParam(':fname', $this->first_name);
        $command->bindParam(':lname', $this->last_name);
        $command->execute();
    }
    
    public function updateCode()
    {
        //Update used account code
        $conn = $this->_conn;
        $sql = "UPDATE account_codes SET `status` = 1 "
                . "WHERE account_code = :account_code";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_code', $this->account_code);
        $command->execute();
    }
    
}