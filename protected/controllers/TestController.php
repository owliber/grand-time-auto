<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 27, 2015
 * @filename TestController.php
 */

class TestController extends Controller
{
    public function actionIndex()
    {
        
        $client_count = 2;
        do {
            $client_id = LapsController::getAvailableSlot(3, $client_count);
//            var_dump($client_id);exit;
            if($client_id === false) 
            {
                $client_count++;
                continue;
            }
            else
            {
                ($client_count > 4) ? $pos1 = 1 : $pos1 = 0; 
                ($client_count % 2 == 0) ? $pos2 = 0 : $pos2 = 1;
                $client = Network::getClientNetwork($client_id, 3, $pos1);
                break;
            }
            
        } while ($client_count <= 5);
        ?>
<pre>
    
<?php
var_dump($client);
?>
</pre>
        <?php
        
    }
}



