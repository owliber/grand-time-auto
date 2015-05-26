<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 20, 2015
 * @filename _second.php
 */
?>
<?php $this->breadcrumbs = array('Race Laps'=>array('#'),'2nd Lap'); ?>
 
<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
    TbHtml::icon(TbHtml::ICON_FLAG) .' <strong>'.$package.'</strong> &mdash; 2nd Lap');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
    //'htmlOptions'=>array('class'=>'span-25'),
));
?>
<?php if(Yii::app()->user->isClient())
{
    $account_id = Yii::app()->user->getUserId();
?>
<div id="lap-level">
    <?php $array2 = Network::showClientNetwork($account_id, 2, 0, 0); ?>
    <div class="<?php echo $array2['cssClass']; ?> yellow span-5"><?php echo $array2['label']; ?></div>
    <?php $array3 = Network::showClientNetwork($account_id, 2, 0, 1); ?>
    <div class="<?php echo $array3['cssClass']; ?> yellow span-5"><?php echo $array3['label']; ?></div>
    <?php $array4 = Network::showClientNetwork($account_id, 2, 1, 0); ?>
    <div class="<?php echo $array4['cssClass']; ?> yellow span-5"><?php echo $array4['label']; ?></div>
    <?php $array5 = Network::showClientNetwork($account_id, 2, 1, 1); ?>
    <div class="<?php echo $array5['cssClass']; ?> yellow span-5"><?php echo $array5['label']; ?></div>
</div>
<div id="lap-level">
    <?php $array0 = Network::showClientNetwork($account_id, 2, 0, 0, true); ?>
    <div class="<?php echo $array0['cssClass']; ?> yellow span-10a"><?php echo $array0['label']; ?></div>
    <?php $array1 = Network::showClientNetwork($account_id, 2, 1, 1, true); ?>
    <div class="<?php echo $array1['cssClass']; ?> yellow span-10a"><?php echo $array1['label']; ?></div>
</div>
<div id="lap-level">
    <div class="yellow span-20a"><b>YOU</b></div>
</div>
<?php
}else  //Admin Access
{?>
<div id="lap-level">
    <?php $array2 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 0, 0); ?>
    <div class="<?php echo $array2['cssClass']; ?> <?php echo Network::getLapStatus($array2['client_id']); ?> yellow span-5"><?php echo $array2['link']; ?></div>
    <?php $array3 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 0, 1); ?>
    <div class="<?php echo $array3['cssClass']; ?> <?php echo Network::getLapStatus($array3['client_id']); ?> yellow span-5"><?php echo $array3['link']; ?></div>
    <?php $array4 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 1, 0); ?>
    <div class="<?php echo $array4['cssClass']; ?> <?php echo Network::getLapStatus($array4['client_id']); ?> yellow span-5"><?php echo $array4['link']; ?></div>
    <?php $array5 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 1, 1); ?>
    <div class="<?php echo $array5['cssClass']; ?> <?php echo Network::getLapStatus($array5['client_id']); ?> yellow span-5"><?php echo $array5['link']; ?></div>
</div>
<div id="lap-level">
    <?php $array0 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 0, 0, true); ?>
    <div class="<?php echo $array0['cssClass']; ?> <?php echo Network::getLapStatus($array0['client_id']); ?> yellow span-10a"><?php echo $array0['link']; ?></div>
    <?php $array1 = Network::showNetwork($clients->account_id, $clients->account_type_id, 2, 1, 1, true); ?>
    <div class="<?php echo $array1['cssClass']; ?> <?php echo Network::getLapStatus($array1['client_id']); ?> yellow span-10a"><?php echo $array1['link']; ?></div>
</div>
<div id="lap-level">
    <div class="<?php echo Network::getLapStatus($clients->account_id); ?> yellow span-20a"><b><?php echo $client_info['first_name'] . ' ('.$client_info['account_code'].')'; ?></b></div>
</div>
<?php 
}
