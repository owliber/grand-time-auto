<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename _client.php
 */
Yii::app()->clientScript->registerScript('ui','
    
    $(\'input[rel="tooltip"]\').tooltip();
    function openModal(body){
        $(".modal-body").html(body);
        $("#payout-details-modal").modal("show");
    }
 ', CClientScript::POS_END);
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'Overall Payout<br /> <strong>P'.$total['total_net_pay'].'</strong>',array(
    'class'=>'first span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Total Received <br /> <strong>P'.$total['total_received'].'</strong>',array(
    'class'=>'second span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_WARNING, 'Total Receivables<br /> <strong>P'.$total['total_receivables'].'</strong>',array(
    'class'=>'last span-5'
)); ?>
<div class="clearfix"></div>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'payout-grid',
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
        array('name'=>'date_created', 
                'header'=>'Payout Date',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'lap_no', 
                'header'=>'Lap',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'gross_pay', 
                'header'=>'Gross Pay',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'deductions', 
                'header'=>'Deductions',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'net_pay', 
                'header'=>'Net Pay',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'status_name', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'visible'=>false,
                'template'=>'{details}',
                'buttons'=>array
                (
                    'details'=>array
                    (
                        'label'=>'View Payout Details ',
                        'icon'=>  TbHtml::ICON_LIST,
                        'url'=>'Yii::app()->createUrl("/payout/details",array("id"=>$data["payout_id"]))',
                        'options'=>array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    
                                    //$("#payout-details-modal").modal("show");
                                    openModal(data.body);
                                 }',
                                'error' => 'function(xhr, status, error) {
                                   alert(error);
                                }',
                            ),
                            
                        ),
                        array('id' => 'send-link-'.uniqid()),
                    ),
                ),
                'header'=>'Options',
                'htmlOptions'=>array('style'=>'width:40px;text-align:center'),
                'headerHtmlOptions'=>array('style'=>'text-align:center'),
            ),
    ),
)); ?>
<?php echo TbHtml::ajaxButton('Send a payout request', CHtml::normalizeUrl(array('payout/sendrequest','id'=>Yii::app()->user->getUserId())), array(
    'type'=>'get',
    'dataType'=>'json',
    'success'=>'function(data){
        alert(data.result_msg);
        $.fn.yiiGridView.update("payout-grid");
    }',
), array(
    'color' => TbHtml::BUTTON_COLOR_DANGER,
    'icon'=>  TbHtml::ICON_ENVELOPE,
    'size'=> TbHtml::BUTTON_SIZE_DEFAULT,
    'style'=> 'margin-top: -60px',
)); ?>
<?php $this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'payout-details-modal',
    'header' => 'Payout Details',
    'content'=>'',
    'footer' => array(
        TbHtml::button('Close', array('data-dismiss' => 'modal')),
    ),
));




