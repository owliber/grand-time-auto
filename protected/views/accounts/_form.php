<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 2, 2015
 * @filename _form.php
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_VERTICAL,
    'id'=>'account-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'clientOptions' => array(
         'validateOnSubmit' => true,
         'validateOnChange' => false,
         'validateOnType' => false
      ),
    'action'=>array('accounts/adduser'),
    'htmlOptions'=>array(),
));
?>
<div class="first well">
<?php echo $form->textFieldControlGroup($model, 'first_name'); ?>
<?php echo $form->textFieldControlGroup($model, 'last_name'); ?>
<?php echo $form->textFieldControlGroup($model, 'email',array('class'=>'span-6')); ?>
<hr />
<h4>Login</h4>
<?php echo $form->dropdownListControlGroup($model,'account_type_id',  $model->listAccountTypes()); ?>
<?php echo $form->textFieldControlGroup($model,'username'); ?>
<?php echo $form->passwordFieldControlGroup($model,'password'); ?>
<?php echo $form->passwordFieldControlGroup($model,'repeat_password'); ?>
<?php $this->endWidget();?>
</div>


