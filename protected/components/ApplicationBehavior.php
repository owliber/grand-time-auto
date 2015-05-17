<?php

/*
 * @author : owliber
 * @date : 2014-01-31
 */

class ApplicationBehavior extends CBehavior
{       
    private $_owner;
        
    public function events() 
    {

        return array(
                 'onBeginRequest'=>'requireLogin',     
        );
    }

    public function requireLogin()
    {
         $owner=$this->getOwner();
         if($owner->user->getIsGuest())
                 $owner->catchAllRequest=array("site/login");
    }
}
