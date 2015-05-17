<?php $this->pageTitle=Yii::app()->name . ' - Forgot Password'; ?>
<?php $this->breadcrumbs = array('Accounts'=>"",'Change Password'); ?>

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
    <legend>Change Password</legend>
    <?php echo $form->passwordFieldControlGroup($model, 'oldpassword'); ?>
    <?php echo $form->passwordFieldControlGroup($model, 'newpassword'); ?>
    <?php echo $form->passwordFieldControlGroup($model, 'confirmpassword'); ?>
    <?php echo $form->hiddenField($model,'account_id'); ?>
</fieldset>

<?php
echo TbHtml::formActions(array(
    TbHtml::submitButton('Change Password', array(
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
        TbHtml::button('Close', array('data-dismiss' => 'modal', 'onclick' => 'window.location.href="../site/logout"')),
    ),
));
?>