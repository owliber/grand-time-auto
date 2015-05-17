<?php

/**
 * @author owliber <owliber@yahoo.com>
 * @date May 18, 2015
 * @filename _test.php
 */

//Test only

function randVowel()
{
	$vowels = array("a", "e", "o", "u");
	return $vowels[array_rand($vowels, 1)];
}

function randConsonant()
{
	$consonants = array("b", "c", "d", "v", "g", "t");
	return $consonants[array_rand($consonants, 1)];
}

$fn = ucfirst("" . randConsonant() . "" . randVowel() . "" . "" . randConsonant() . "" . randVowel() . "" . randVowel() . "");
$ln = ucfirst("" . randConsonant() . "" . randVowel() . "" . "" . randConsonant() . "" . randVowel() . "" . randVowel() . "");

$model->first_name = $fn;
$model->last_name = $ln;
$model->email = uniqid().'@test.com';
$temp = new AccountCodesModel();
$model->account_code = $temp->getCode();
$model->referrer_id = 619;



