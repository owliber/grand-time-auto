<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 1, 2015
 * @filename AccountDetails.php
 */

class AccountDetails extends CActiveRecord {
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'account_details';
    }
}



