<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date June 2, 2015
 * @filename _form.php
 */

echo TbHtml::label('Account Code', 'account_code');
echo TbHtml::textField('account_code', '', array('class'=>'span-6')) . '<br />';
echo TbHtml::label('Head Count', 'head_count');
echo TbHtml::dropDownList('head_count', '', array(3=>3,6=>6),array('class'=>'span-2'));


