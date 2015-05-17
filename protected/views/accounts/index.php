<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Aug 11, 2014
 * @filename index
 */
?>
<?php
Yii::app()->clientScript->registerScript('ui','
    
    $(\'input[rel="tooltip"]\').tooltip(); 
//    $("#account-dialog").modal({
//        backdrop: true,
//        keyboard: true,
//        show : false,
//    }).css({
//        width: "600px",
//        "margin-left": function () {
//            return -($(this).width() / 2);
//        }
//    });
                
 ', CClientScript::POS_END);
?>
<?php $this->breadcrumbs = array('Admin'=>array('#'),'Account Management'); ?>
<?php
echo TbHtml::button('Create New Account', array(
    'id'=>'new-account',
    'color' => TbHtml::BUTTON_COLOR_DANGER,
    'style' => 'margin-bottom: 10px;',
    'data-toggle' => 'modal',
    'data-target' => '#account-dialog',
    'style'=>'margin:20px 20px 0',
    'class'=>'pull-right',
));
?>
<?php
Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
        '<h4>System Accounts</h4> Create, enable or disable system accounts.'
);
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
)); ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'accounts-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array('name'=>'username', 
                'header'=>'Username',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account', 
                'header'=>'Account Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_type_name', 
                'header'=>'Account Type',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'status', 
                'header'=>'Status',
                'htmlOptions'=>array('style'=>'text-align:center'),
                'headerHtmlOptions' => array('style' => 'text-align:center'),
            ),
        array('name'=>'date_last_login', 
                'header'=>'Last Login',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{disable}{enable}',
                'buttons'=>array
                (
                    'enable'=>array
                    (
                        'label'=>'Enable Account',
                        'icon'=>  TbHtml::ICON_CHECK,
                        'visible'=>'$data["status"] == "Inactive" || $data["status"]=="Pending"',
                        'url'=>'Yii::app()->createUrl("/accounts/enable",array("id"=>$data["account_id"]))',
                        'confirm'=>'Are you sure you want to enable this account?',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        location.reload();
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
                    'disable'=>array
                    (
                        'label'=>'Disable Account ',
                        'icon'=>  TbHtml::ICON_REMOVE_CIRCLE,
                        'visible'=>'$data["status"] == "Active"',
                        'url'=>'Yii::app()->createUrl("/accounts/disable", array("id" =>$data["account_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                            'ajax' => array(
                                'type' => 'GET',
                                'dataType'=>'json',
                                'url' => 'js:$(this).attr("href")',
                                'success' => 'function(data){
                                    if(data.result_code == 0)
                                    {
                                        alert(data.result_msg);
                                        location.reload();
                                    }
                                    else
                                    {
                                        alert(data.result_msg);
                                    }
                                 }',
                            ),

                        ),
                        array('id' => 'send-link-'.uniqid())
                    )
                    
                ),
                'header'=>'Options',
                'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                'headerHtmlOptions'=>array('style'=>'text-align:center'),
            ),
        
    ),
));

$this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'account-dialog',
    'header' => 'New Account',
    'content' => $this->renderPartial('_form', array('model'=>$model), true),
    'footer' => array(
        TbHtml::ajaxButton('Add User', 'adduser', array(
            'data'=>'js:$("#account-form").serialize()',
            'type' => 'post',
            'dataType'=>'json',
            'success'=>'function(data){
                if(data.result_code == 0)
                {
                    $.fn.yiiGridView.update("accounts-grid");
                    $("#account-form")[0].reset();
                    alert(data.result_msg);
                    $("#account-dialog").modal("hide");
                    
                }
                else
                {
                    $.each(data, function(key, val) {
                        $("#"+key+"_em_").text(val);
                        $(".control-group").removeClass("control-group").addClass("control-group error");
                        $("#"+key+"_em_").removeClass("help-block").addClass("help-block control-group error");
                        $("#"+key+"_em_").show();
                    });
                }
                
            }',
        ), array(
            //'confirm'=>'Are you sure you want to continue?',
            'id'=>'ajaxBtnRegister',
            'color'=>  TbHtml::BUTTON_COLOR_DANGER
        )),
        TbHtml::button('Cancel', array('data-dismiss' => 'modal')),
    ),
));