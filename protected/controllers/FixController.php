<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 18, 2015
 * @filename FixController.php
 */

class FixController extends Controller
{
    public function actionAddDeductions()
    {
        $model = new TempModel();
        $payouts = $model->getPayouts();
        $i = 1;
        foreach($payouts as $row)
        {
            $model->payout_id = $row['payout_id'];
            $model->payout_deduction_id = 10;
            $model->amount = 3600;
            $model->description = '2nd Lap Auto Entry Fee';
            $model->createDeduction();
            
            if(!$model->hasErrors())
            {
                echo $i . ' Deduction ID 10 added for payout_id '.$row['payout_id'].'<br />';
            }
            $i++;
        }
        
    }
    
    public function actionUnprocessed()
    {
        $model = new Clients();
        
        $clients = $model->getClientListByDate();
        $lap_no = 1;
        $newrows=array();
                
        foreach($clients as $client)
        {
            $account_id = $client['account_id'];
            $network = Network::getNetworkCount($account_id, $lap_no);
            
            $table_count = count($network);
            if($table_count > 2)
            {
                $rows = $client;
                $rows['lap_no'] = $lap_no;
                $rows['total_clients'] = $table_count;
                $newrows[] = $rows;

            }
           
        }
        
        $dataProvider = new CArrayDataProvider($newrows,array(
                                'keyField'=>false,
                                'pagination'=>array(
                                    'pageSize'=>25,
                                ),
                            ));
        
        $this->render('lists',array(
                'dataProvider'=>$dataProvider,
                'model'=>$model,
            ));
    }
    
    public function actionRequeue()
    {
        $model = new Clients();
        
        $clients = $model->getClientListByDate();
        $lap_no = 1;
        $newrows=array();
        $job = new Jobs();
        
        foreach($clients as $client)
        {
            $account_id = $client['account_id'];
            $network = Network::getNetworkCount($account_id, $lap_no);
            
            $table_count = count($network);
            if($table_count > 2)
            {
                $rows = $client;
                $rows['lap_no'] = $lap_no;
                $rows['total_clients'] = $table_count;
                $newrows[] = $rows;
                
                /* Add account on the job queues */
                $job->account_id = $account_id;
                $job->client_id = end($network[2]);
                if($table_count > 2 && $table_count < 6)
                    $table_count = 3;
                $job->table_count = $table_count;
                $job->insert_queue();

            }
           
        }
        
    }
}



