<?php
header('Connection: close');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

require_once('parameters.php');

$commentText = trim($_POST['commentText']);
$commentEmail = filter_var($_POST['commentEmail'], FILTER_SANITIZE_EMAIL);

//Verify input
if(strlen($commentText) === 0)
{
	echo '<span class="commentError">Empty text</span>';
	return;
}
if(filter_var($commentEmail, FILTER_VALIDATE_EMAIL) === FALSE)
{
	echo '<span class="commentError">Invalid email address</span>';
	return;
}
if(filter_var($siteUrl, FILTER_VALIDATE_URL) === FALSE)
{
	echo '<span class="commentError">Unvalid site url, contact website owner</span>';
	return;
}

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

$verificationCode = substr(sha1(time().rand().$_POST['commentEmail'].$_SERVER['REMOTE_ADDR']), 0, 10);

$res = @mysql_query('INSERT INTO comments
(SiteID, SiteUrl, CommentIP, CommentDate, CommentText, CommentEmail, VerificationCode)
VALUES
('.$siteID.',
\''.mysql_real_escape_string($siteUrl).'\',
\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
NOW(),
\''.mysql_real_escape_string($commentText).'\',
\''.mysql_real_escape_string($commentEmail).'\',
\''.mysql_real_escape_string($verificationCode).'\')');

if(!$res) {
	echo '<span class="commentError">'.mysql_error().'</span>';
	return;
}
$id = mysql_insert_id();

$mailed = mail($commentEmail,
		'Verify your comment',
		'To verify your comment on '.$siteUrl.'
Click here:
'.$service_url.'/verify.php?cid='.$id.'&sid='.$siteID.'&code='.$verificationCode,
		'From: '.$service_email);
if(!$mailed)
{
	echo '<span class="commentError">Failed to send verification email, try again</span>';
	return;
}

echo '<span class="commentOk">Comment awaiting verification, check your email</span>';
