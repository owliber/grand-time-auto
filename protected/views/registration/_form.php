<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 13, 2015
 * @filename index.php
 * @controller RegistrationController
 */

echo TbHtml::alert(TbHtml::ALERT_COLOR_DANGER, 'Please fill-up all required fields!',array(
    'id'=>'process-id'
)); 

?>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
    'id'=>'jumpstart-form',
    'enableAjaxValidation' => true,
     'enableClientValidation' => true,
     'clientOptions' => array(
         'validateOnSubmit' => true,
         'validateOnChange' => false,
         'validateOnType' => false
      ),
    'action'=>array('registration/register'),
));
?>

<?php echo TbHtml::hiddenField('RegistrationForm[pos]','',array('id'=>'RegistrationForm_pos')); ?>
<?php echo TbHtml::hiddenField('RegistrationForm[account_id]','',array('id'=>'RegistrationForm_account_id')); ?>
<?php echo TbHtml::hiddenField('RegistrationForm[sponsor_id]','',array('id'=>'RegistrationForm_sponsor_id')); ?>
<?php echo TbHtml::hiddenField('RegistrationForm[account_type_id]','',array('id'=>'RegistrationForm_account_type_id')); ?>
<?php echo $form->textFieldControlGroup($model,'first_name'); ?>
<?php echo $form->textFieldControlGroup($model,'last_name'); ?>
<?php echo $form->textFieldControlGroup($model,'account_code'); ?>
<?php echo $form->textFieldControlGroup($model, 'email',array('class'=>'span-8')); ?>
<?php echo $form->hiddenField($model, 'referrer_id'); ?>
<div class="control-group">
    <?php echo TbHtml::label('Sponsor <span class="required">*</span>', 'referrer_name',array('class'=>'control-label required')); ?>
    <div class="controls">
        <?php
        $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
            'model'=>$model,
            'attribute'=>'referrer_name',
            'sourceUrl'=>  Yii::app()->createUrl('registration/sponsors'),
            'options'=>array(
                'minLength'=>'3',
                'showAnim'=>'fold',
                'focus' => 'js:function(event, ui){$("#RegistrationForm_referrer_name").val(ui.item["value"])}',
                'select' => 'js:function(event, ui){$("#RegistrationForm_referrer_id").val(ui.item["id"]); }',
                'appendTo'=>'#regform-dialog',
            ),
            'htmlOptions'=>array(
                'class'=>'span3',
                'rel'=>'tooltip',
                'title'=>'Please type the sponsor\'s name or code.',
                'autocomplete'=>'off',
                'id'=>'referrer_name',
            ),        
        )); 
        ?>
    </div>
</div>
<?php $this->endWidget();
