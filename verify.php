<?php
header('Connection: close');

$siteID = intval($_GET['sid']);
$commentID = intval($_GET['cid']);
$code = $_GET['code'];

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

$res = @mysql_query('UPDATE comments
	SET
	VerifiedDate = NOW(),
	VerifiedIP = \''.$_SERVER['REMOTE_ADDR'].'\'
	WHERE ID='.$commentID.'
	AND SiteID='.$siteID.'
	AND VerificationCode=\''.mysql_real_escape_string($code).'\'
	AND VerifiedIP IS NULL');

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

$result = @mysql_query('SELECT ID, SiteUrl FROM comments
	WHERE ID='.$commentID.'
	AND SiteID='.$siteID);

while ($row = mysql_fetch_assoc($result)) {
	header('Location: '.$row['SiteUrl']);
	echo '<a href="'.htmlentities($row['SiteUrl']).'#comment'.$row['ID'].'">Continue to '.htmlentities($row['SiteUrl']).'</a>';
}

echo "<p>Comment verified, thank you!</p>";

