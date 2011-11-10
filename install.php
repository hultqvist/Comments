<?php

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'")
 or die(mysql_error());

//mysql_query('DROP TABLE comments;')
// or die(mysql_error());

mysql_query('CREATE TABLE comments (
	ID INT NOT NULL auto_increment,
	SiteID INT NOT NULL,
	SiteUrl TINYTEXT NOT NULL,
	CommentIP TINYTEXT,
	CommentText text NOT NULL,
	CommentDate DATETIME NOT NULL,
	CommentEmail TINYTEXT,
	VerificationCode TINYBLOB,
	VerifiedDate DATETIME,
	VerifiedIP TINYTEXT,
	PRIMARY KEY (ID)
)')
 or die(mysql_error());

mysql_close();
