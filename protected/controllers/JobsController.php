<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 4, 2015
 * @filename JobController.php
 */

class JobsController extends Controller
{
    public $layout = 'column2';
    
    public function actionIndex()
    {
        $this->initialize();
        $model = new Jobs();
        $result = $model->get_queues();
        $model->cron_id = 1;
        $last_run = $model->get_last_run();
        $dataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>25,
                                ),
                            ));
        
        $this->render('index',array(
                'dataProvider'=>$dataProvider,
                'model'=>$model,
                'lastrun'=>$last_run,
            ));
    }
    
}




