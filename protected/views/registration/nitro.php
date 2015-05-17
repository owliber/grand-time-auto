<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename nitro.php
 */

$this->breadcrumbs = array('Registration'=>array('#'),'VIP Nitro');

Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_ERROR,
    '<strong>'. TbHtml::icon(TbHtml::ICON_EXCLAMATION_SIGN) . ' Notice:</strong> Sorry this page is under development.');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
));



