<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 6, 2015
 * @filename client.php
 */

$this->breadcrumbs = array('Welcome '.ucfirst(Yii::app()->user->getClientName()).'! You are last logged on '.date('M d, Y h:i a',strtotime($lastLogin)));
$this->pageTitle= 'GTA Client Portal';
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Welcome to Grand Time Automobile Group!'); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_WARNING, 'Overall Payout<br /> <strong>P'.$total['total_net_pay'].'</strong>',array(
    'class'=>'first span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Total Received Payout<br /> <strong>P'.$total['total_received'].'</strong>',array(
    'class'=>'second span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'Total Receivables<rb /> <strong>P'.$total['total_receivables'].'</strong>',array(
    'class'=>'last span-5'
));