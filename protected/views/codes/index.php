<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 13, 2015
 * @filename index.php
 */

$this->breadcrumbs = array('Admin'=>array('#'),'Account Codes'); ?>
 
<?php echo TbHtml::linkButton('Print', array(
    'url'=>array('codes/batches'),
    'icon'=>  TbHtml::ICON_PRINT,
    'color' => TbHtml::BUTTON_COLOR_DANGER,
    'style'=>'margin:20px 20px 0',
    'class'=>'pull-right',
    'data-toggle' => 'modal',
    'data-target' => '#myModal',
)); ?>
<?php echo TbHtml::linkButton('Code Batches', array(
    'url'=>array('codes/batches'),
    'icon'=>  TbHtml::ICON_CHEVRON_LEFT,
    'color' => TbHtml::BUTTON_COLOR_DANGER,
    'style'=>'margin:20px -10px',
    'class'=>'pull-right',
)); ?>


<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
    '<h4>'.$model->account_type_name.' Account Codes Batch No. '.$model->code_batch_id.'</h4> Summary of generated account codes for <b>'.$model->account_type_name.'</b> accounts.');
?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'codes-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
//        array(
//            'header' => '#',
//            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + 1)',
//            'htmlOptions' => array('style' => 'text-align:center;'),
//            'headerHtmlOptions' => array('style' => 'text-align:center'),
//        ),
        array('name'=>'account_code_id', 
                'header'=>'#',
                'htmlOptions'=>array('style'=>'text-align:center;'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'account_code', 
                'header'=>'Account Code',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),       
        array('name'=>'status', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
    ),
));
