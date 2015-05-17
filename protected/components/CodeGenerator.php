<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Apr 13, 2015
 * @filename CodeGenerator.php
 */

class CodeGenerator extends Controller
{

    /**
     * 
     * @param int $length
     * @param int $num
     * @return type
     */        
    public function generateCode($prefix,$length, $num, $is_numeric = false)
    {
        $numeric_set = '0123456789';
        $character_set = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        
        if($is_numeric)
            $code_sets = $numeric_set;
        else
            $code_sets = $numeric_set . $character_set;
        
        $codes = array();
        $num_length = strlen($code_sets);
        $num_created = 0;
        
        while ($num_created < $num)
        {
            
            $code = $prefix . CodeGenerator::generate_codes($length, $code_sets, $num_length);
            if (isset($codes[$code]))
                continue;
            $codes[$code] = true;
            $num_created++;
        }
        return array_keys($codes);
    }
    
    public function generate_codes($length, $code_sets, $num_length)
    {
        $code = '';
        while (strlen($code) < $length)
            $code .= CodeGenerator::get_random_code($code_sets, $num_length);
        return $code;
    }
    
    public function get_random_code($code_sets, $num_length)
    {
        return substr($code_sets, mt_rand(0, $num_length - 1), 1);
    }
}

