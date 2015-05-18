<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename profile.php
 */

$this->breadcrumbs = array('Accounts'=>array('#'),'Update Profile');

Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
    '<h4>Update Profile</h4> Update personal details and other required information.');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
));

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
    'id'=>'profile-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'clientOptions' => array(
         'validateOnSubmit' => true,
         'validateOnChange' => true,
         'validateOnType' => false
      ),
    //'action'=>array('accounts/profile'),
    'htmlOptions'=>array(),
));
?>
<fieldset>
<?php
if(isset($model->account_code))
{
    echo $form->uneditableFieldControlGroup($model,'account_code');
}
?>
<?php echo $form->hiddenField($model, 'account_id'); ?>
<?php echo $form->textFieldControlGroup($model, 'first_name'); ?>
<?php echo $form->textFieldControlGroup($model, 'middle_name'); ?>
<?php echo $form->textFieldControlGroup($model, 'last_name'); ?>
<?php echo $form->textFieldControlGroup($model, 'mobile_no'); ?>
<div class="control-group">
    <?php echo CHtml::label('&nbsp;Birthdate&nbsp;<span class="required">*</span>','ProfileForm_birthdate',array('class'=>'control-label required'));?>
    <div class="controls">
    <?php
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'model' => $model,
        'attribute' => 'birthdate',
        'value'=> '',
        'htmlOptions' => array(
            'size' => '10',
            'maxlength' => '10',
            'readonly' => true,
            'class'=>'span-4'
        ),
        'options' => array(
            'showOn' => 'button',
            'changeMonth' => true,
            'changeYear' => true,
            'buttonImage' => Yii::app()->request->baseUrl.'/images/calendar.png',
            'buttonImageOnly' => true,
            'dateFormat' => 'yy-mm-dd',
            'maxDate' => '0',
            'yearRange' => '1950:' . date('Y'),
        )
    ));
    ?>
    </div>
</div>
<?php echo $form->textFieldControlGroup($model, 'email',array('class'=>'span-6','prepend'=>  TbHtml::ICON(TbHtml::ICON_ENVELOPE))); ?>
<?php echo $form->textFieldControlGroup($model,'address1',array('class'=>'span-12')); ?>
<?php echo $form->textFieldControlGroup($model,'address2',array('class'=>'span-12')); ?>
<?php if(Yii::app()->user->isAdmin() || Yii::app()->user->isSalesManager())
{?>    
<?php echo $form->hiddenField($model,'referrer_id'); ?>
<div class="control-group">
    <?php echo TbHtml::label('Sponsor\'s Name <span class="required">*</span>', 'referrer_name',array('class'=>'control-label required')); ?>
    <div class="controls">
        <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model'=>$model,
            'attribute'=>'referrer_name',
            'sourceUrl'=>  Yii::app()->createUrl('accounts/sponsors'),
            'options'=>array(
                'minLength'=>'3',
                'showAnim'=>'fold',
                'focus' => 'js:function(event, ui){$("#ProfileForm_referrer_name").val(ui.item["value"])}',
                'select' => 'js:function(event, ui){$("#ProfileForm_referrer_id").val(ui.item["id"]); }',
            ),
            'htmlOptions'=>array(
                'class'=>'span3',
                'rel'=>'tooltip',
                'title'=>'Please type the sponsor\'s name or code.',
                'autocomplete'=>'off',
            ),        
        )); 
        ?>
    </div>
</div>
<?php
}?>
</fieldset>
<?php
$activateButton = "";
if($model->status == 0)
{
    $activateButton = TbHtml::ajaxButton('Resend Activation', array('/accounts/resend','id'=>$model->account_id), array(
            'data'=>array(
                'first_name'=>$model->first_name,
                'email'=>$model->email,
                'account_code'=>$model->account_code,
            ),
            'type' => 'get',
            'dataType'=>'json',
            'success'=>'function(data){
                alert(data.result_msg);
            }',
        ), array(
            'id'=>'ajaxBtnResend',
            'color'=>  TbHtml::BUTTON_COLOR_INFO,
        ));
}
?>
<?php echo TbHtml::formActions(array(
    TbHtml::submitButton('Update', array('color' => TbHtml::BUTTON_COLOR_DANGER)),
    $activateButton,
    TbHtml::resetButton('Reset'),
)); ?>
<?php $this->endWidget();?>
<?php
    $this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'dialog-message',
    'header' => $this->dialogTitle,
    'content' => $this->dialogMessage,
    'footer'=>array(
        TbHtml::button('Close', array('data-dismiss' => 'modal')),
    ),
    'show'=>$this->dialogOpen
));


