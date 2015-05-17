<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 28, 2015
 * @filename PayoutController.php
 */

class PayoutController extends Controller
{
    public $layout = 'column2';
    
    public function actionIndex()
    {
        $model = new PayoutModel();
        
        //Set defaults
        $result = array();
        $total = array(
            'total_net_pay'=>'0.00',
            'total_processed'=>'0.00',
            'total_requests'=>'0.00'
        );
        
        if(Yii::app()->user->isClient())
        {
            $model->account_id = Yii::app()->user->getUserId();
            $result = $model->getPayoutByClient();
            $total = $model->getPayoutTotalByClient();
        }
        else
        {
            if(isset(Yii::app()->session['payouts'])) 
            {
                $model->attributes = Yii::app()->session['payouts'];
                if($model->validate())
                {
                    $result = $model->getPayoutByDate();
                    $total = $model->getPayoutTotalByDate();
                }
            }
            else
            {
                $model->date_from = date('Y-m-d');
                $model->date_to = date('Y-m-d');

                $result = $model->getPayoutRequests();
                $total = $model->getPayoutTotal();
            }
            
            if(isset($_POST['PayoutModel']))
            {
                if(isset(Yii::app()->session['payouts'])) 
                    unset(Yii::app()->session['payouts']);
                
                $model->attributes = $_POST['PayoutModel'];
                if($model->validate())
                {
                    Yii::app()->session['payouts'] = $model->attributes;
                    $result = $model->getPayoutByDate();
                    $total = $model->getPayoutTotalByDate();
                }

            }
            
        }
        
        
        $dataProvider = new CArrayDataProvider($result,array(
                                    'keyField'=>false,
                                    'pagination'=>array(
                                        'pageSize'=>25,
                                    ),
                                ));
        
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
            'model'=>$model,
            'total'=>$total,
        ));
    }
    
    public function actionDetails()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PayoutModel();
            $model->payout_id = $_GET['id'];
            $summary = $model->getPayoutSummary();
            $details = $model->getPayoutDetails();
            
            $dataProvider = new CArrayDataProvider($details,array(
                                    'keyField'=>false,
                                    'pagination'=>array(
                                        'pageSize'=>10,
                                    ),
                                ));
            
            //Enable to add another ajax button in the modal
            //Yii::app()->clientscript->scriptMap['jquery.js'] = false;
            $body = $this->renderPartial('_details', array(
                'label' => 'Success!',
                'dataProvider'=>$dataProvider,
                'model'=>$model,
                'summary'=>$summary,
            ), true, false); // processOutput

            echo CJSON::encode(array(
                'body' => $body,
            ));
            exit;
        }
        else
             throw new CHttpException('403', 'Forbidden access.');
    }
    
    public function actionProcess()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PayoutModel();
            $model->payout_id = $_GET['id'];
            $model->processPayout();
            
            if(!$model->hasErrors())
            {
                $result_code = 0;
                $result_msg = 'Payout was successfully processed.';
            }
            else
            {
                $result_code = 1;
                $result_msg = 'An error encountered while processing payout. Please report to IT.';
            }
            
            echo CJSON::encode(array(
                'result_code'=>$result_code,
                'result_msg'=>$result_msg,
            ));
        }
    }
    
    public static function generatePayout($payout_code, $account_id)
    {
        //Get payout matrix
        $payout = new PayoutModel();
        $payout->matrix_code = $payout_code;
        $payout->account_id = $account_id;
        
        /* Get the payout matrix */
        $matrix = $payout->getPayoutMatrix();
        
        foreach($matrix as $val)
        {
            $payout->matrix_id = $val['payout_matrix_id'];
            $payout->total_amount = $val['total_amount'];
            $payout->head_count = $val['head_count'];
            $payout->lap_no = $val['lap_no'];
            $payout->is_visible = $val['is_visible'];
        }
        
        /* Get all deductions */
        $deductions = $payout->getDeductions();
        
        $total_deductions = 0;
        foreach($deductions as $row)
        {
            $total_deductions += $row['deduction_amount'];
        }
        $payout->total_deductions = $total_deductions;
        $payout->deduction_details = $deductions;
        $net_payout = $payout->total_amount - $payout->total_deductions;
        $payout->generatePayout();
        
        /*send email notification */
        $clients = new Clients();
        $clients->account_id = $account_id;
        $client_info = $clients->getClientInfo();
        $params['client_name'] = $client_info['first_name'];
        $params['account_type_id'] = $client_info['account_type_id'];
        $params['email'] = $client_info['email'];
        $params['payout_amount'] = $net_payout;  
        
        //Notify only clients with non-zero payouts
        if($net_payout > 0) Mailer::sendPayoutInfo($params);
        
    }
    
    public function actionClients()
    {
        if(Yii::app()->request->isAjaxRequest && isset($_GET['term']))
        {
            $model = new Clients();

            $result = $model->getPayoutClients($_GET['term']);

            if(count($result)>0)
            {
                foreach($result as $row)
                {
                    $arr[] = array(
                        'id'=>$row['client_id'],
                        'value'=>$row['account_code'],
                        'label'=>$row['client'],
                    );
                }

                echo CJSON::encode($arr);
                Yii::app()->end();
            }
            
        }
    }
    
    public function actionSendRequest()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $model = new PayoutModel();
            $model->account_id  = $_GET['id'];
            $model->requestPayout();
            
            if(!$model->hasErrors())
            {
                $result_code = 0;
                $result_msg = "You have successfully sent an email request to process your payouts.";
            }
            else
            {
                $result_code = 1;
                $result_msg = "A problem encountered while sending your request. Please contact GTA IT.";
            }
            
            echo CJSON::encode(array('result_code'=>$result_code,'result_msg'=>$result_msg));
        }
        else
        {
            throw new CHttpException('Invalid access');
        }
    }
        
}



