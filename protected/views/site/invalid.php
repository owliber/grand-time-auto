<?php
$this->pageTitle=Yii::app()->name . ' - Error | Invalid URL';
$this->breadcrumbs=array(
	'Error - Invalid URL',
);
?>

<div class="error">
    <?php Yii::app()->user->setFlash('error', '<strong><h2>Urg! Trying to access something?</h2></strong> The page you are trying to access is either not accessible or does not actually exist.'); ?>
    <?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=> false, //'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); ?>
    <p>You are activities are being logged and monitored. <br />
        Username : <?php echo Yii::app()->user->getId(); ?><br />
        IP Address: <?php echo $_SERVER['REMOTE_ADDR']; ?><br />
        Date Accessed : <?php echo date('Y-m-d H:i s'); ?><br />
        Accessed URL : <?php echo urldecode($_GET['url']); ?>
    </p>
    <?php //Tools::log(15, urldecode($_GET['url']), 1); ?>
</div>
