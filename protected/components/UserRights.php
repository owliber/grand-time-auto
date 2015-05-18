<?php

/*
 * @author : owliber
 * @date : 2014-01-22
 */

class UserRights extends CWebUser
{
   
    public static function hasAccess()
    {
        $model = new AccessRights();
        if($model->checkAccess(Yii::app()->session['account_type_id']) && !Yii::app()->user->isGuest)
            return true;
        else
            return false;
            
    }
        
    public static function isSalesManager()
    {
        if(Yii::app()->session['account_type_id'] == 8 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public static function isAdmin()
    {
        if(Yii::app()->session['account_type_id'] == 1 || Yii::app()->session['account_type_id'] == 2 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function isClient()
    {
        if(Yii::app()->session['account_type_id'] == 5 || Yii::app()->session['account_type_id'] == 6 || Yii::app()->session['account_type_id'] == 7 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function getUserId() {
        return Yii::app()->session['account_id'];
    }
    
    public function getAccountType()
    {
        $model = new AccountTypes();
        $model->account_type_id = Yii::app()->session['account_type_id'];
        $result = $model->getAccountTypeName();
        return $result;
    }
    
    public function getClientName()
    {
        $client = AccountDetails::model()->findByAttributes(array('account_id'=>$this->getUserId()));
        if(count($client)>0)
            return $client->first_name;
        else
            return Yii::app()->user->getName();
    }
}

