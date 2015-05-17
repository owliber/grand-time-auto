<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Jul 25, 2014
 * @filename RequireLogin
 */

class RequireLogin extends CBehavior
{
    public function attach($owner)
    {
        $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
    }
    
    public function handleBeginRequest($event)
    {
        $controller = Yii::app()->request->getPathInfo();
        $allowed = array(
            'site/login',
            'test/index',
            'accounts/forgotpassword',
            'accounts/validate',
            'accounts/reset',
            'accounts/activate',
            'cron/autocomplete',
        );
        if (Yii::app()->user->isGuest && !in_array($controller, $allowed))
        {
            Yii::app()->user->loginRequired();
        }
    }
}
