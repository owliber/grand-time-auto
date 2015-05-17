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
}



