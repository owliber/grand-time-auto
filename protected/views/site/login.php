<?php
$this->pageTitle=Yii::app()->name . ' - Login';
?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'login-form',
    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
        ));
?>

<fieldset>
    <legend>Client Login</legend>
    <?php echo $form->textFieldControlGroup($model, 'username'); ?>
    <?php echo $form->passwordFieldControlGroup($model, 'password'); ?>
    <?php echo $form->checkBoxControlGroup($model, 'rememberMe'); ?>
    <div class="control-group">
        <div class="controls">
            <?php echo CHtml::link('Forgot Password? Click here to reset.', array('accounts/forgotpassword'),array('style'=>'font-size:11px')); ?>
        </div>
    </div>
</fieldset>
<?php
echo TbHtml::formActions(array(
    TbHtml::submitButton('Login', array('color' => TbHtml::BUTTON_COLOR_DANGER,'size'=> TbHtml::BUTTON_SIZE_LARGE)),
));
?>

<?php $this->endWidget(); ?>

