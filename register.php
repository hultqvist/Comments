<?php

$siteUrl = FALSE;
if(isset($_POST['url']))
	$siteUrl = $_POST['url'];
if(filter_var($siteUrl, FILTER_VALIDATE_URL) === FALSE)
	$siteUrl = FALSE;
if($siteUrl === FALSE)
{
	echo "Invalid URL";
	return;
}

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

//Verify pageUrl
$res = @mysql_query('INSERT INTO Sites (SiteUrl) VALUES (\''.mysql_real_escape_string($siteUrl).'\')');
if(!$res)
{
	echo mysql_error();
	return;
}
$id = mysql_insert_id();

header('Content-Type: text/plain');
?>
<!-- Start of comment code -->
<div id="comments">Loading comments...</div>
<script type="text/javascript" src="<?php echo $service_url;?>/script.php?sid=<?php echo $id;?>" async="async"></script>
<!-- End of comment code -->
