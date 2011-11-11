<?php

//Database and service parameters
require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

function GenerateAndSendVerificationCode($email, $url)
{
	$code = substr(sha1(time().rand().$email.$_SERVER['REMOTE_ADDR']), 0, 10);
	@mysql_query('REPLACE Authors SET Email=\''.mysql_real_escape_String($email).'\', VerifyDate=NOW(), VerifyCode=\''.mysql_real_escape_String($code).'\'')
	 or die('<div class="commentError">'.mysql_error().'</div>');

	//Email verification link
	$mailed = mail($email,
		'Verify your comment',
		'To verify the comment you made on '.$url.'

Click here to login and review your comments:
'.service_url.'/authorize/?email='.urlencode($email).'&code='.$code,
		'From: '.service_email)
	or die('<div class="commentError">Failed to send verification email, try again</div>');
}

//Return the email address verified by session cookie
function GetSessionEmail()
{
	$email = isset($_COOKIE['email'])? $_COOKIE['email'] : null;
	$session = isset($_COOKIE['session'])? $_COOKIE['session'] : null;
	$res = @mysql_query('SELECT * FROM Authors
		WHERE Email=\''.mysql_real_escape_string($email).'\'
		AND Session=\''.mysql_real_escape_string($session).'\'')
		or die('<div class="commentError">'.mysql_error().'</div>');
	if(mysql_num_rows($res) !== 1)
	{
		setcookie("session", null, time()-24*3600);
		define('sessionEmail', null);
		return NULL;
	}
	define('sessionEmail', $email);
	return $email;
}

