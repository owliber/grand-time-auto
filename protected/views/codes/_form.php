<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 14, 2015
 * @filename _form.php
 */


$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'layout' => TbHtml::FORM_LAYOUT_VERTICAL,
));
?>
<?php echo $form->dropDownListControlGroup($model,'account_type_id',  $model->listAccountCodeTypes(),array(
    'class'=>'span4',
)); ?>
<?php echo $form->textFieldControlGroup($model, 'quantity'); ?>
<?php $this->endWidget(); 

