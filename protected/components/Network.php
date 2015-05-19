<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 15, 2015
 * @filename Network.php
 */

class Network extends Controller
{
    public static function getClients($account_id, $lap, $level = 0)
    {
        $model = new Clients();
        $parent = array();
        $children = array();
        $model->account_id = $account_id;
        $model->lap_no = $lap;
        
        $i = 0;
        $level++;
        $clients = $model->getLapClients();
        foreach ($clients as $key => $val)
        {
            $parent[$i]['account_id'] = $account_id;
            $parent[$i]['client_id'] = $clients[$key]["client_id"];
            $parent[$i]['client_name'] = $clients[$key]["client_name"];
            $parent[$i]['position']= $clients[$key]["pos"];
            $parent[$i]['sponsor_id'] = $clients[$key]["sponsor_id"];
            $parent[$i]['account_code'] = $clients[$key]["account_code"];
            $parent[$i]['account_type'] = $clients[$key]["account_type_id"];
            $parent[$i]['level'] = $level;
            $children = array_merge($children, Network::getClients($clients[$key]["client_id"], $lap, $level));
            $i++;
            
        }
        
        $finalTree = array_merge($parent, $children);
        
        return $finalTree;
    }
    
    public static function getClientCount($sponsor_id)
    {
        $model = new Clients();
        $model->sponsor_id = $sponsor_id;
        $result = $model->getClientCount();
        return $result;
    }
    
    public static function getClientNetwork($sponsor_id, $lap_no, $pos)
    {
        $model = new Clients();
        $model->lap_no = $lap_no;
        $model->account_id = $sponsor_id;
        $rawData = $model->getLapClients();  
        $rows = array();
        foreach($rawData as $row)
        {
            if($row['pos'] == $pos)
            {
                $rows[] = $row;
            }
        }
        return $rows;
    }
        
    public static function showLink($id,$sid,$atid,$pos)
    {
        return TbHtml::ajaxLink('Register', 
                        array('getinfo'),
                        array('type' => 'GET',
                            'data' => array(
                                'id'=>$id,
                                'sid'=>$sid,
                                'atid'=>$atid,
                                'pos'=>$pos,
                            ),
                            'dataType' => 'json',
                            'success' => 'function(data){
                                $(".modal-header").html(data.header);
                                $("#RegistrationForm_pos").val(data.position);
                                $("#RegistrationForm_account_id").val(data.account_id);
                                $("#RegistrationForm_sponsor_id").val(data.sponsor_id);
                                $("#RegistrationForm_account_type_id").val(data.account_type_id);
                                $("#regform-dialog").modal("show");
                            }',
                            'beforeSend' => 'function() { 
                                $("#ajax-loader").addClass("loading");
                             }',
                             'complete' => 'function() {
                               $("#ajax-loader").removeClass("loading");
                             }', 
                        ),
                        array('id'=> uniqid())
                        );
    }
    
    public static function getNetworkCount($account_id, $lap_no, $level = 0)
    {
        $model = new Clients();
        $model->account_id = $account_id;
        $model->lap_no = $lap_no;
        $parent = array();
        $children = array();
        
        $i = 0;
        $level++;
        
        if ($level <= 2)
        {
            $downlines = $model->getDownlines();
            foreach ($downlines as $key => $val)
            {
                $parent[$i][$level] = $downlines[$key]["client_id"];
                $children = array_merge($children, Network::getNetworkCount($downlines[$key]["client_id"], $lap_no, $level));
                $i++;
            }
        }
        
        $finalTree = array_merge($parent, $children);
                
        return $finalTree;
    }
    
    public static function getLapCount($lap_no)
    {
        $model = new LapModel();
        $model->lap_no = $lap_no;
        switch($model->lap_no)
        {
            case 1: $model->lap_no = 'lap_one';break;
            case 2: $model->lap_no = 'lap_two';break;
            case 3: $model->lap_no = 'lap_three';break;
        }
        $result = $model->getLapNoInfo();
        return count($result);
    }
    
    public static function getLapStatus($account_id)
    {
        $model = new LapModel();
        $model->account_id = $account_id;
        $result = $model->getLapInfo();
        $cssClass = '';
        
        if(count($result)>0)
        {
            foreach($result as $row)
            {
                $lap_no = $row['lap_no'];
                switch($lap_no)
                {
                    case 1: //completed lap 1
                        $cssClass = 'yellow';
                        break;
                    case 2: // completed lap 2
                        $cssClass = 'green';
                        break;
                    case 3: // exit for payout
                        $cssClass = 'green';
                        break;
                }
                    
            }
            return $cssClass;
        }
        
    }
    
    public static function showNetwork($account_id, $account_type_id, $lap_no, $pos1, $pos2, $is_base = false, $is_form = false)
    {
        switch($lap_no)
        {
            case 1: ($is_form) ? $view = "jumpstart" : $view = "first";break;
            case 2: $view = "second"; break;
            case 3: $view = "third"; break;
        }
        
        if(Network::getClientCount($account_id)>0)
        {
            //Check if base account has client at position 0 - L
            $rawData = Network::getClientNetwork($account_id,$lap_no,$pos1);
            if(count($rawData)>0)
            {
                $client_id = $rawData[0]['client_id'];
                $sponsor_id = $rawData[0]['sponsor_id'];
                
                
                if($is_base)
                {
                    $link = TbHtml::link($rawData[0]['account_code'], 
                                array($view,
                                    'id'=> $rawData[0]['client_id'] 
                        ));
                    $cssClass = "";
                    $client_name = $rawData[0]['client_name'];
                }
                else
                {
                     //Get sponsor network at position 0 - L
                    $result = Network::getClientNetwork($client_id,$lap_no,$pos2);
                    if(count($result)>0)
                    {
                        $link = TbHtml::link($result[0]['account_code'], 
                                array($view,
                                    'id'=> $result[0]['client_id'] 
                        ));
                        
                        $client_id = $result[0]['client_id'];
                        $cssClass = "";
                        $client_name = $result[0]['client_name'];
                        

                    }
                    else
                    {
                        if($is_form)
                            $link = Network::showLink($sponsor_id,$client_id,$account_type_id,$pos2);
                        else
                            $link = '&nbsp;';
                        
                        $cssClass = "empty-gray";
                        $client_name = 'Open';
                    }
                }
               
            }
            else
            {
                if($is_base)
                {   
//                    if($lap_no > 1)
//                        $link = '&nbsp;';
//                    else
//                        $link = Network::showLink($account_id,$account_id,$account_type_id,$pos1);
                    if($is_form)
                        $link = Network::showLink($account_id,$account_id,$account_type_id,$pos1);
                    else
                        $link = '&nbsp;';
                }
                else
                {
                    $link = '&nbsp;';
                    
                }
                
                $cssClass = "empty-gray";
                $client_id = null;
                $client_name = 'Open';
            }

        }
        else
        {
            if($is_base)
            {   
//                if($lap_no > 1)
//                    $link = '&nbsp;';
//                else
//                    $link = Network::showLink($account_id,$account_id,$account_type_id,$pos1);
                if($is_form)
                    $link = Network::showLink($account_id,$account_id,$account_type_id,$pos1);
                else
                    $link = '&nbsp;';
            }
            else
            {
                $link = '&nbsp;';
                
            }
            
            $cssClass = "empty-gray";
            $client_id = null;
            $client_name = 'Open';
                
        }
        $array = array(
            'link'=>$link,
            'client_id'=>$client_id,
            'client_name'=>$client_name,
            'cssClass'=>$cssClass
        );            
        return $array;
    }
    
    public static function showClientNetwork($account_id, $lap_no, $pos1, $pos2, $is_base = false)
    {
        if(Network::getClientCount($account_id)>0)
        {
            $rawData = Network::getClientNetwork($account_id,$lap_no,$pos1);
            if(count($rawData)>0)
            {
                $client_id = $rawData[0]['client_id'];
                
                if($is_base)
                {
                    $label = $rawData[0]['account_code'];
                    $cssClass = "";
                }
                else
                {
                     //Get sponsor network at position 0 - L
                    $result = Network::getClientNetwork($client_id,$lap_no,$pos2);
                    if(count($result)>0)
                    {
                        $label = $result[0]['account_code'];
                        $client_id = $result[0]['client_id'];
                        $cssClass = "";
                    }
                    else
                    {
                        $label = '&nbsp;';
                        $cssClass = "empty-gray";
                    }
                }
               
            }
            else
            {
                $cssClass = "empty-gray";
                $label = '&nbsp;';
                $client_id = null;
            }

        }
        else
        {
            $cssClass = "empty-gray";
            $label = '&nbsp;';
            $client_id = null;
            
                
        }
        
        $array = array('label'=>$label,'client_id'=>$client_id,'cssClass'=>$cssClass);
        return $array;
    }
   
}



