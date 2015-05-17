<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 30, 2015
 * @filename _info.php
 */
$this->breadcrumbs = array('Race Laps'=>array('#'),'Notice');

Yii::app()->user->setFlash(TbHtml::ALERT_COLOR_ERROR,
    '<strong>'. TbHtml::icon(TbHtml::ICON_EXCLAMATION_SIGN) . ' Notice:</strong> Sorry there are no clients yet for this lap.');
?>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, // use transitions?
    'closeText'=>false,
));



