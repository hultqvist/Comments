<?php
$siteID = intval($_GET['id']);
?>
<h1><?php echo $siteID; ?> from: <?php echo $_SERVER['HTTP_REFERER']; ?></h1>

<?php
require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());


$query='
	SELECT * FROM TABLE comments
	WHERE SiteID = '.$siteID.'
	AND
 (
	ID int NOT NULL auto_increment,
	Comment text NOT NULL,
	CommentDate DATETIME NOT NULL,
	CommentEmail TINYTEXT,
	VerificationCode TINYBLOB,
	VerifiedDate DATETIME,
	VerifiedIP TINYTEXT,
	PRIMARY KEY (ID)
)';
mysql_query($query) or die(mysql_error());
mysql_close();
