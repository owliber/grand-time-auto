<?php
/* @var $this SiteController */
$this->breadcrumbs = array('Welcome '.ucfirst(Yii::app()->user->getClientName()).'! You are last logged on '.date('M d, Y h:i a',strtotime($lastLogin)));
$this->pageTitle= 'GTA Client Portal';
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_INFO, 'Welcome to Grand Time Automobile Group!'); ?>
<?php if(Yii::app()->user->isClient()) $title = 'Your Lap Results'; else $title = 'Lap Results'; ?>
<?php 
    $this->widget('yiiwheels.widgets.google.WhVisualizationChart', array(
        'visualization' => 'BarChart',
         'data' => array(
             array('Laps', 'Clients'),
             array('Lap 1', $lap[1]),
             array('Lap 2', $lap[2]),
             array('Lap 3', $lap[3]),
         ),
         'options' => array(
             'title' => $title,
             //'colors'=>array('#ff0000','#fff200','#098509'),
             //'is3D'=>true

         )
     )); 
?>
<?php
if(Yii::app()->user->isClient() || Yii::app()->user->isAdmin())
{
    Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_SUCCESS,
        'Total Payout &mdash; <strong>P'.$total.'</strong>'
    );

    $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true,
        'fade'=>true, // use transitions?
        'closeText'=>false,
        'htmlOptions'=>array(
            //'class'=>'span-6',

        )
    )); 
}