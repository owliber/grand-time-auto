<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 21, 2015
 * @filename _table.php
 */

$this->breadcrumbs = array('Lap Results'=>array('#'), $account_type . ' Table View'); ?>
 
<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_INFO,
    '<strong>'.$account_type.' (Lap '.$lap_no.') </strong> &mdash; ' . $client_info['first_name'] . '&nbsp;' . $client_info['last_name']);
?>

<?php echo TbHtml::linkButton('Forward', array(
    'url'=>'#',
    'icon'=>  TbHtml::ICON_CHEVRON_RIGHT,
    'color' => TbHtml::BUTTON_COLOR_INFO,
    'class'=>'pull-right',
    'onclick'=>'history.go(1)',
    'style'=>'margin:10px 20px 0',
)); ?>
<?php echo TbHtml::linkButton('Back', array(
    'url'=>'#',
    'icon'=>  TbHtml::ICON_CHEVRON_LEFT,
    'color' => TbHtml::BUTTON_COLOR_INFO,
    'class'=>'pull-right',
    'onclick'=>'history.go(-1)',
    'style'=>'margin:10px -10px 0',
)); ?>
    
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>

<div id="lap-level">
    <?php $array2 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 0, 0); ?>
    <div class="<?php echo $array2['cssClass']; ?> <?php echo Network::getLapStatus($array2['client_id']); ?> red span-5"><?php echo $array2['link']; ?><br /><?php echo TbHtml::labelTb($array2['client_name']); ?></div>
    <?php $array3 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 0, 1); ?>
    <div class="<?php echo $array3['cssClass']; ?> <?php echo Network::getLapStatus($array3['client_id']); ?> red span-5"><?php echo $array3['link']; ?><br /><?php echo TbHtml::labelTb($array3['client_name']); ?></div>
    <?php $array4 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 1, 0); ?>
    <div class="<?php echo $array4['cssClass']; ?> <?php echo Network::getLapStatus($array4['client_id']); ?> red span-5"><?php echo $array4['link']; ?><br /><?php echo TbHtml::labelTb($array4['client_name']); ?></div>
    <?php $array5 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 1, 1); ?>
    <div class="<?php echo $array5['cssClass']; ?> <?php echo Network::getLapStatus($array5['client_id']); ?> red span-5"><?php echo $array5['link']; ?><br /><?php echo TbHtml::labelTb($array5['client_name']); ?></div>
</div>
<div id="lap-level">
    <?php $array0 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 0, 0, true); ?>
    <div class="<?php echo $array0['cssClass']; ?> <?php echo Network::getLapStatus($array0['client_id']); ?> red span-10a"><?php echo $array0['link']; ?><br /><?php echo TbHtml::labelTb($array0['client_name']); ?></div>
    <?php $array1 = Network::showTable($clients->account_id, $clients->account_type_id, $lap_no, 1, 1, true); ?>
    <div class="<?php echo $array1['cssClass']; ?> <?php echo Network::getLapStatus($array1['client_id']); ?> red span-10a"><?php echo $array1['link']; ?><br /><?php echo TbHtml::labelTb($array1['client_name']); ?></div>
</div>
<div id="lap-level">
    <div class="<?php echo Network::getLapStatus($clients->account_id); ?> red span-20a"><b><?php echo $client_info['first_name'] . ' ' . $client_info['last_name'] .' ('.$client_info['account_code'].')'; ?></b></div>
</div>



