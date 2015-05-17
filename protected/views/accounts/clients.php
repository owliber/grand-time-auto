<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 3, 2015
 * @filename clients.php
 */
Yii::app()->clientScript->registerScript('ui','$(\'input[rel="tooltip"]\').tooltip();', CClientScript::POS_END);
$this->breadcrumbs = array('Admin'=>array('#'),'Manage Clients');

Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_WARNING,
    '<h4>Client Management</h4> Manage and update client\'s account and other information.');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
));?>

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
    'htmlOptions'=>array(
        'class'=>'well',
    )
));
?>
<?php echo $form->textField($model, 'search_key',array('rel'=>'tooltip','placeholder'=>'Enter name, username or code')); ?>
<?php echo $form->dropdownList($model,'account_type_id',  $model->listClientTypes()); ?>

<?php echo TbHtml::submitButton('Select', array('color'=>  TbHtml::BUTTON_COLOR_DANGER));?>
<?php $this->endWidget(); ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'clients-grid',
    'type'=>  TbHtml::GRID_TYPE_HOVER,
    'dataProvider'=>$dataProvider,
    'enablePagination' => true,
    'columns'=>array(
        array(
            'header' => '#',
            'value' => '$row + ($this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + 1)',
            'htmlOptions' => array('style' => 'text-align:center;'),
            'headerHtmlOptions' => array('style' => 'text-align:center'),
        ),
        array('name'=>'client_name', 
                'header'=>'Account Name',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'account_code', 
                'header'=>'Account Code',
                'htmlOptions'=>array('style'=>'text-align:left'),
                'headerHtmlOptions' => array('style' => 'text-align:left'),
            ),
        array('name'=>'username', 
                'header'=>'Username',
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
                'template'=>'{update}',
                'buttons'=>array
                (
                    'update'=>array
                    (
                        'label'=>'Update Client Info',
                        'icon'=>  TbHtml::ICON_EDIT,
                        'url'=>'Yii::app()->createUrl("/accounts/profile",array("id"=>$data["account_id"]))',
                        'options' => array(
                            'class'=>"btn btn-small",
                        ),
                        array('id' => 'send-link-'.uniqid())
                    ),
                ),
                'header'=>'Options',
                'htmlOptions'=>array('style'=>'width:80px;text-align:center'),
                'headerHtmlOptions'=>array('style'=>'text-align:center'),
            ),
        
    ),
));



