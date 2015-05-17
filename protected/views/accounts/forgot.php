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
    <legend>Forgot Password</legend>
    <?php echo $form->textFieldControlGroup($model, 'account_code', array('help'=>'Please enter your username or account code.','class'=>'span-10')); ?>
    <?php //echo $form->textFieldControlGroup($model, 'email',array('help'=>'Please enter your email address.','class'=>'span-10')); ?>
</fieldset>
<?php
echo TbHtml::formActions(array(
    TbHtml::submitButton('Submit', array('color' => TbHtml::BUTTON_COLOR_DANGER)),
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


