<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename _admin.php
 */

Yii::app()->clientScript->registerScript('ui','
    
    $(\'input[rel="tooltip"]\').tooltip();
    function openModal(body){
        $(".modal-body").html(body);
        $("#payout-details-modal").modal("show");
    }
 ', CClientScript::POS_END);
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'Total Payouts<br /> <strong>P'.$total['total_net_pay'].'</strong>',array(
    'class'=>'last span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Total Processed Payouts<br /> <strong>P'.$total['total_processed'].'</strong>',array(
    'class'=>'last span-5'
)); ?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_DANGER, 'Total Payout Requests<br /> <strong>P'.$total['total_requests'].'</strong>',array(
    'class'=>'last span-5'
));?>
<div class="clearfix"></div>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_SEARCH,
    'id'=>'search-form',
    'enableAjaxValidation' => true,
     'enableClientValidation' => true,
     'clientOptions' => array(
         'validateOnSubmit' => true,
         'validateOnChange' => false,
         'validateOnType' => false
      ),
));
?>
<div class="well">
<?php
echo $form->textField($model, 'search_key',array('rel'=>'tooltip','class'=>'span-5','title'=>'Please type a name or account code.'));
echo CHtml::label('&nbsp;From&nbsp;','date_from');
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'date_from',
    'value'=> $model->date_from,
    'htmlOptions' => array(
        'size' => '10',
        'maxlength' => '10',
        'readonly' => true,
        'class'=>'span-3'
    ),
    'options' => array(
        'showOn' => 'button',
        'buttonImageOnly' => true,
        'changeMonth' => true,
        'changeYear' => true,
        //'buttonText' => 'Select from date',
        'buttonImage' => Yii::app()->request->baseUrl.'/images/calendar.png',
        'buttonImageOnly' => true,
        'dateFormat' => 'yy-mm-dd',
        'maxDate' => '0',
        'yearRange' => '2015:' . date('Y'),
    )
));
?>
<?php
echo CHtml::label('&nbsp;To&nbsp;','date_to');
$this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'model' => $model,
    'attribute' => 'date_to',
    'value'=> $model->date_to,
    'htmlOptions' => array(
        'size' => '10',
        'maxlength' => '10',
        'readonly' => true,
        'class'=>'span-3'
    ),
    'options' => array(
        'showOn' => 'button',
        'changeMonth' => true,
        'changeYear' => true,
        //'buttonText' => 'Select to date',
        'buttonImage' => Yii::app()->request->baseUrl.'/images/calendar.png',
        'buttonImageOnly' => true,
        'dateFormat' => 'yy-mm-dd',
        'maxDate' => '0',
        'yearRange' => '2015:' . date('Y'),
    )
));
?>

<?php echo $form->dropDownList($model, 'status', array(1=>'Requested', 0=>'Pending', 2=>'Processed'),array('class'=>'span-3')); ?>&nbsp;
<?php echo TbHtml::submitButton('Submit', array('color'=>  TbHtml::BUTTON_COLOR_DANGER)); ?>
 
<?php $this->endWidget();?>
</div>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'payout-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'htmlOptions'=>array('style'=>'font-size:11px'),
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
        array('name'=>'account_name', 
                'header'=>'Client Name',
                'htmlOptions'=>array('style'=>'text-align:left;'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_code', 
                'header'=>'Client Code',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'lap_no', 
                'header'=>'Lap',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'total_amount', 
                'header'=>'Total Amount',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'total_deductions', 
                'header'=>'Total Deductions',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
            ),
        array('name'=>'net_pay', 
                'header'=>'Net Pay',
                'htmlOptions'=>array('style'=>'text-align:right'),
                'headerHtmlOptions' => array('style' => 'text-align:right'),
                //'footer'=>'<strong>'.$total.'</strong>',
                //'footerHtmlOptions'=>array('style'=>'text-align:right; font-size:14px'),
            ),
        array('name'=>'status_name', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{details}{cancel}{process}',
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
                    'process'=>array
                    (
                        'label'=>'Process Payout',
                        'icon'=>  TbHtml::ICON_CHECK,
                        'visible'=>'$data["status"] == 0 || $data["status"] == 1',
                        'url'=>'Yii::app()->createUrl("/payout/process",array("id"=>$data["payout_id"]))',
                        'options' => array(
                            'confirm'=>'Are you sure you want to process this payout?',
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        //$.fn.yiiGridView.update("payout-grid");
                                        $("#search-form").submit();
                                    }
                                    else
                                    {
                                        alert(data.result_msg);
                                    }
                                 }',
                            ),
                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                    'cancel'=>array
                    (
                        'label'=>'Cancel Payout',
                        'icon'=>  TbHtml::ICON_TRASH,
                        'visible'=>'($data["status"] == 0 || $data["status"] == 1) && ' . Yii::app()->user->isAdmin(),
                        'url'=>'Yii::app()->createUrl("/payout/cancelrequest",array("id"=>$data["payout_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    $("#hdn_payout_id").val(data.payout_id);
                                    $("#cancel-payout-modal").modal("show");
                                 }',
                            ),
                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'Options',
                'headerHtmlOptions'=>array('style'=>'text-align:center'),
            ),
    ),
)); 
?>

<?php $this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'payout-details-modal',
    'header' => 'Payout Details',
    'content'=>'',
    'footer' => array(
        TbHtml::button('Close', array('data-dismiss' => 'modal')),
    ),
    ));
?>
<?php
$modal_content = TbHtml::hiddenField('hdn_payout_id');
$modal_content .= TbHtml::label('Reason for Cancellation', 'reason');
$modal_content .= TbHtml::textArea('reason', '', array('rows'=>5,'cols'=>50,'style'=>'width:95%'));

?>
<?php $this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'cancel-payout-modal',
    'header' => 'Cancel Payout',
    'content'=>$modal_content,
    'footer' => array(
        TbHtml::ajaxSubmitButton('Cancel Payout','cancel',
                 array(
                     'data'=>array(
                         'id'=>'js:function(){return $("#hdn_payout_id").val()}',
                         'reason'=>'js:function(){return $("#reason").val()}'
                     ),
                     'dataType'=>'json',
                     'type'=>'post',
                     'success'=>'function(data) {
                           alert(data.result_msg);
                           $("#cancel-payout-modal").modal("hide");
                           $.fn.yiiGridView.update("payout-grid");
                      }',   
                      'beforeSend'=>'function(){
                           if($("#reason").val() == ""){
                                alert("Please enter a reason for the cancellation.");
                                return false;
                           }
                      }'
                     ),array('id'=>'ajaxBtnCancel-'.  uniqid(),'color'=>  TbHtml::BUTTON_COLOR_DANGER,
                         'confirm'=>'Are you sure you want to continue?',
                         )),
        TbHtml::button('Close', array('data-dismiss' => 'modal')),
    ),
    ));


