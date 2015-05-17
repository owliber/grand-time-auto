<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 5, 2015
 * @filename TestModel.php
 */

class TestModel extends CFormModel
{
    public $_conn;
    
    public function __construct() {
        $this->_conn = Yii::app()->db;
    }
    
    public function selectDate()
    {
        $conn = $this->_conn;
        $sql = "SELECT current_timestamp();";
        $command = $conn->createCommand($sql);
        return $command->queryAll();
    }
}



