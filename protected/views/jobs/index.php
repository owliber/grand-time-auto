<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 4, 2015
 * @filename index.php
 */

$this->breadcrumbs = array('Admin'=>array('#'),'Job Queues'); ?>
<?php
echo TbHtml::ajaxButton('Process Jobs', array('/cron/process'), array(
            'data'=>array('render'=>1),
            'type' => 'get',
            'dataType'=>'json',
            'success'=>'function(data){
                //$("#ajax-loader").hide(); 
                //alert(data);
                $("#last_run_date").text("Last Job Run: "+data.lastrun);
                $.fn.yiiGridView.update("queue-grid");
            }',
            'beforeSend'=>'function(){                        
                  //$("#ajax-loader").show();
                  $("#last_run_date").text("Processing...");
             }'
        ), array(
            'id'=>'ajaxBtnProcessJobs',
            'color'=>  TbHtml::BUTTON_COLOR_DANGER,
            'style'=>'margin:20px 20px 0',
            'class'=>'pull-right',
        ));

echo TbHtml::button('Refresh', array(
    'onclick'=>'$.fn.yiiGridView.update("queue-grid");',
    'class'=>'pull-right',
    'style'=>'margin:20px -10px 0 0',
    'color'=>  TbHtml::BUTTON_COLOR_WARNING,
));

echo TbHtml::labelTb('Last Job Run: '.$lastrun, array(
    'color'=>  TbHtml::LABEL_COLOR_SUCCESS,
    'style'=>'margin:25px 10px 0',
    'class'=>'pull-right',
    'id'=>'last_run_date',
));
?>
<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_ERROR,
        '<h4>Unprocessed Jobs</h4> List of all unprocessed clients for payout and table completion.'
);
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'queue-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array(
            'header' => '#',
            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + 1)',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),
        array('name'=>'account_code', 
                'header'=>'Account Code',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_name', 
                'header'=>'Account Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'client_name', 
                'header'=>'Client Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
    ))
);



