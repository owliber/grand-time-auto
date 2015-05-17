<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 28, 2015
 * @filename index.php
 */

$this->breadcrumbs = array('Payouts'=>array('#'),'Payout Summary');
?>

<h4> Payout Summary</h4>

<?php if(Yii::app()->user->isClient())
{
    $this->renderPartial('_client',array(
        'model'=>$model,
        'dataProvider'=>$dataProvider,
        'total'=>$total,
    ));
}else
{
    $this->renderPartial('_admin',array(
        'model'=>$model,
        'dataProvider'=>$dataProvider,
        'total'=>$total,
    ));
}



