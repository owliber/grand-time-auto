<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 13, 2015
 * @filename CodesController.php
 */

class CodesController extends Controller
{
    public $layout = 'column2';
    
    public function actionIndex()
    {
        $this->initialize();
        
        if(isset($_GET['id']) && is_numeric($_GET['id']))
        {
            $model = new AccountCodesModel();
            $account = new AccountTypes();
            $model->code_batch_id = $_GET['id'];
            $account->account_type_id = $_GET['account_type'];
            
            $account_type = $account->getAccountTypeName();
            $model->account_type_name = $account_type;
            
            $result = $model->selectCodesByBatch();

            $dataProvider = new CArrayDataProvider($result,array(
                                    'keyField'=>false,
                                    'pagination'=>array(
                                        'pageSize'=>50,
                                    ),
                                ));

            $this->render('index',array(
                    'dataProvider'=>$dataProvider,
                    'model'=>$model,
                ));
        }
        
        
    }
    
    public function actionBatches()
    {
        $this->initialize();
        
        $model = new AccountCodesModel();
        $result = $model->selectAllBatches();
        
        $dataProvider = new CArrayDataProvider($result,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>50,
                                ),
                            ));
        
        $this->render('batches',array(
                'dataProvider'=>$dataProvider,
                'model'=>$model,
            ));
    }
    
    public function actionGenerate()
    {
        
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new AccountCodesModel();
            $model->account_type_id = $_GET['account_type_id'];
            $model->quantity = $_GET['quantity'];
            
            switch($model->account_type_id )
            {
                case 5: $prefix = 'JS';
                    break;
                case 6: $prefix = 'MT';
                    break;
                case 7: $prefix = 'VN';
                    break;
            }
                
            $model->generateBatch($prefix);
            
            if(!$model->hasErrors())
            {
                $result_code = 0;
                $result_msg = 'Account codes was successfully generated';
            }
            else
            {
                $result_code = 1;
                $result_msg = 'An error encountered while generating account codes.';
            }
            
            echo CJSON::encode(array('result_code'=>$result_code,'result_msg'=>$result_msg));
        }       
    }
}




