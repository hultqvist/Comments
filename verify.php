<?php
//This is called when a person clicks the verification link in an email.

header('Connection: close');

$commentID = intval($_GET['cid']);
$code = $_GET['code'];

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

$res = @mysql_query('UPDATE Comments
	SET
	VerifiedDate = NOW(),
	VerifiedIP = \''.$_SERVER['REMOTE_ADDR'].'\'
	WHERE CommentID='.$commentID.'
	AND VerificationCode=\''.mysql_real_escape_string($code).'\'
	AND VerifiedIP IS NULL
	');

if(!$res) {
	echo mysql_error();
	return;
}
$aff = mysql_affected_rows();
if($aff != 1)
{
	echo "<p>Invalid verification link</p>";
	return;
}

$result = @mysql_query('SELECT PageUrl FROM Comments WHERE CommentID='.$commentID);

$row = mysql_fetch_assoc($result);
if($row != NULL){
	$link=$row['PageUrl'].'#comment'.$commentID;
	header('Location: '.$link);
	echo '<a href="'.htmlentities($link).'">Continue to '.htmlentities($link).'</a>';
}

echo "<p>Comment verified, thank you!</p>";

