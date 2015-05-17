<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 3, 2015
 * @filename ProfileForm.php
 */

class ProfileForm extends CFormModel
{
    public $_conn;
    public $account_id;
    public $referrer_id;
    public $referrer_name;
    public $account_code;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $email;
    public $mobile_no;
    public $birthdate;
    public $address1;
    public $address2;
    public $status;
        
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function rules()
    {
        return array(
            array('account_id,first_name,last_name,email,mobile_no,birthdate','required'),
            array('referrer_id,middle_name,address1,address2','safe'),
            array('email','email'),
            
        );
    }
    
    public function attributeLabels() {
        return array(
            'first_name'=>'First Name',
            'last_name'=>'Last Name',
            'email'=>'Email Address',
            'mobile_no'=>'Mobile No.',
            'birthdate'=>'Birthdate',
            'address1'=>'Bldg/Floor/Unit/Street',
            'address2'=>'Brgy/City/Province',
            'referrer_name'=>'Sponsor\'s Name'
        );
    }
    
    public function getProfileInfo()
    {
        $conn = $this->_conn;
        $sql = "SELECT
                    a.referrer_id,
                    CONCAT(COALESCE(ad1.first_name,''),' ',COALESCE(ad1.last_name,'')) AS referrer_name,
                    a.account_id,
                    a.account_code,
                    ad.first_name,
                    ad.last_name,
                    ad.middle_name,
                    ad.mobile_no,
                    ad.birthdate,
                    ad.email,
                    ad.address1,
                    ad.address2,
                    a.status
                  FROM accounts a
                    INNER JOIN account_details ad
                      ON a.account_id = ad.account_id
                    LEFT JOIN account_details ad1 ON a.referrer_id = ad1.account_id
                  WHERE a.account_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindParam(':account_id',$this->account_id);
        return $command->queryAll();
    }
    
    public function updateProfile()
    {
        $conn = $this->_conn;
        $trx = $conn->beginTransaction();
        $sql = "UPDATE account_details ad
                INNER JOIN accounts a
                  ON ad.account_id = a.account_id
                SET ad.first_name = :first_name,
                    ad.last_name = :last_name,
                    ad.middle_name = :middle_name,
                    ad.email = :email,
                    ad.mobile_no = :mobile_no,
                    ad.birthdate = :birthdate,
                    ad.address1 = :address1,
                    ad.address2 = :address2,
                    a.referrer_id = :referrer_id
                WHERE ad.account_id = :account_id;";
        $command = $conn->createCommand($sql);
        $command->bindValues(array(
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'middle_name'=>$this->middle_name,
            'email'=>$this->email,
            'mobile_no'=>$this->mobile_no,
            'birthdate'=>$this->birthdate,
            'address1'=>$this->address1,
            'address2'=>$this->address2,
            'account_id'=>$this->account_id,
            'referrer_id'=>$this->referrer_id,
        ));
        $command->execute();
        
        try
        {
            $trx->commit();
            Tools::log(12, $this->account_id, 1);
        } catch (Exception $ex) {
            $trx->rollback();
            Tools::log(12, $ex->getMessage(), 2);
        }
    }
}


