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
    
    public function actionAdd()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new Jobs();
            $client = new Clients();
            
            if(isset($_GET['account_code']) && isset($_GET['head_count']))
            {
                $client->account_code = $_GET['account_code'];
                $result = $client->getClientInfoByCode();
                $account_id = $result['account_id'];
                $table_count = $_GET['head_count'];
                $network = Network::getNetworkCount($account_id, 1);
                $table_count == 3 ? $client_id = end($network[2]) : $client_id = end($network[5]);
                $model->account_id = $account_id;
                
                $model->client_id = $client_id;
                $model->table_count = $table_count;
                $model->insert_queue();
                
                if(!$model->hasErrors())
                {
                    $result_code = 0;
                    $result_msg = 'Account is successfully queued';
                }
                else
                {
                    $result_code = 1;
                    $result_msg = $model->getErrors();
                }
                
                echo CJSON::encode(array('result_code'=>$result_code,'result_msg'=>$result_msg));
            }
            else
            {
                throw  new CHttpException('Invalid Parameters');
            }
            
        }
        else
        {
            throw  new CHttpException('Invalid Request');
        }
    }
    
}




