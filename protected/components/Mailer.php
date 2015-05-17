<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date Feb 18, 2015
 * @filename Mailer.php
 */

class Mailer
{
    CONST REGISTRATION_TMPL = 1;
    CONST VERIFY_ACCT_TMPL = 2;
    CONST RESET_LINK_TMPL = 3;
    CONST PAYOUT_TMPL = 4;
    
    public function sendAccountInfo($params)
    {
        $email = new EmailLogModel();
        $message_template = Tools::get_template(self::REGISTRATION_TMPL);      
        $activationLink = 'http://clients.grandtimeauto.com/accounts/activate?code='.$params['account_code'].'&email='.$params['email_address'];
        
        $placeholders = array(
            'CLIENT' => $params['client_name'],
            'ACCOUNT_CODE' => $params['account_code'],
            'ACTIVATION_LINK' => $activationLink,
            'TEMP_PASSWORD' => $params['temp_password'],
        );

        foreach($placeholders as $key => $value){
            $message_template = str_replace('{'.$key.'}', $value, $message_template);
        }
        
        /* Send mail directly, uncomment later if mailer cron is enabled */
        $sender = 'noreply@grandtimeauto.com';
        $sender_name = 'GTA Admin';
        $recipient = $params['email_address'];
        $subject = 'GTA - Account Activation';

        $email->email_recipient = $params['email_address'];
        $email->email_subject = $subject;
        $email->message = $message_template;
        $email->insertEmails();
        
        if(Tools::mailer_on())
            Mailer::sendMails($sender, $sender_name, $recipient, $subject, $message_template);
        
    }
    
    public function sendPayoutInfo($params)
    {
        $email = new EmailLogModel();
        $message_template = Tools::get_template(self::PAYOUT_TMPL);      
        
        switch($params['account_type_id'])
        {
            case 5: $matrix = "Jump Start";break;
            case 6: $matrix = "Main Turbo"; break;
            case 7: $matrix = "VIP Nitro";break;
        }
        
        $placeholders = array(
            'CLIENT' => $params['client_name'],
            'PAYOUT_AMOUNT' => $params['payout_amount'],
            'MATRIX_NAME' => $matrix,
        );

        foreach($placeholders as $key => $value){
            $message_template = str_replace('{'.$key.'}', $value, $message_template);
        }
        
        /* Send mail directly, uncomment later if mailer cron is enabled */
        $sender = 'noreply@grandtimeauto.com';
        $sender_name = 'GTA Admin';
        $subject = 'New payout from GTA Group!';
        $recipient = $params['email'];
        
        $email->email_recipient = $recipient;
        $email->email_subject = $subject;
        $email->message = $message_template;
        $email->insertEmails();
        
        if(Tools::mailer_on())
            Mailer::sendMails($sender, $sender_name, $recipient, $subject, $message_template);
    }
        
    public function sendResetLink($params)
    {
        $message_template = Tools::get_template(self::RESET_LINK_TMPL); 
        $resetLink = 'http://clients.grandtimeauto.com/accounts/validate?key=';
        $email = $params['email'];
        
        $key = $params['key'];
        
        $placeholders = array(
            'RESETLINK'=>$resetLink,
            'KEY' => $key,
        );

        foreach($placeholders as $key => $value){
            $message_template = str_replace('{'.$key.'}', $value, $message_template);
        }
        
        $sender = 'noreply@grandtimeauto.com';
        $sender_name = 'GTA Admin';
        $recipient = $email;
        $subject = 'GTA - Password Reset';

        if(Tools::mailer_on())
            Mailer::sendMails($sender, $sender_name, $recipient, $subject, $message_template);
    }
        
    /**
     * Send all queued emails
     * @param type $sender
     * @param type $sender_name
     * @param type $recipient
     * @param type $subject
     * @param type $message_body
     */
    public static function sendMails($sender, $sender_name, $recipient, $subject, $message_body)
    {
        Yii::app()->mailer->Host = 'localhost';
        Yii::app()->mailer->IsHTML(TRUE);
        Yii::app()->mailer->IsMail();
        Yii::app()->mailer->From = $sender;
        Yii::app()->mailer->FromName = $sender_name;
        Yii::app()->mailer->AddAddress($recipient);
        Yii::app()->mailer->Subject = $subject;
        Yii::app()->mailer->Body = $message_body;
        Yii::app()->mailer->Send();
        Yii::app()->mailer->ClearAddresses();
    }
}