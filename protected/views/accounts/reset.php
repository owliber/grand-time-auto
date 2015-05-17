<?php
$this->pageTitle=Yii::app()->name . ' - Forgot Password';
?>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'forgot-form',
    'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
        ));
?>

<fieldset>
    <legend>New Password</legend>
    <?php echo $form->passwordFieldControlGroup($model, 'newpassword'); ?>
    <?php echo $form->passwordFieldControlGroup($model, 'confirmpassword'); ?>
    <?php echo $form->hiddenField($model, 'hashkey'); ?>
    <?php echo $form->hiddenField($model,'email'); ?>
</fieldset>

<?php
echo TbHtml::formActions(array(
    TbHtml::submitButton('Reset Password', array(
        'color' => TbHtml::BUTTON_COLOR_DANGER,
    )),
));
?>

<?php $this->endWidget(); ?>

<?php
$this->widget('bootstrap.widgets.TbModal', array(
    'id' => 'dialog-message',
    'show' => $this->dialogOpen,
    'header' => $this->dialogTitle,
    'content' => $this->dialogMessage,
    'footer' => array(
        TbHtml::button('Close', array('data-dismiss' => 'modal', 'onclick' => 'window.location.href="login"')),
    ),
));
?>