<?php
// Create tables in database
// config.php must contain valid MySql connection information.

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'")
 or die(mysql_error());

//mysql_query('DROP TABLE Comments;') or print(mysql_error());
//mysql_query('DROP TABLE Sites;') or print(mysql_error());
//mysql_query('DROP TABLE Authors;') or print(mysql_error());

mysql_query('CREATE TABLE Comments (
	CommentID INT NOT NULL auto_increment,
	SiteID INT NOT NULL,
	PagePath VARCHAR(255) NOT NULL,
	CommentIP VARBINARY(16) NOT NULL,
	CommentEmail VARCHAR(255) NOT NULL,
	CommentText VARCHAR(32767) NOT NULL,
	CommentDate DATETIME NOT NULL,
	VerifiedDate DATETIME,
	VerifiedIP VARBINARY(16),
	PRIMARY KEY (CommentID)
) DEFAULT CHARSET=utf8')
 or print(mysql_error());

mysql_query('CREATE TABLE Sites (
	SiteID INT NOT NULL auto_increment,
	SiteUrl VARCHAR(255) NOT NULL,
	AdminEmail VARCHAR(255),
	PRIMARY KEY (SiteID)
) DEFAULT CHARSET=utf8')
 or print(mysql_error());

mysql_query('INSERT INTO Sites (SiteID, SiteUrl,AdminEmail) VALUES (1, \''.mysql_real_escape_string(service_url).'\',\''.mysql_real_escape_string(service_email).'\')')
 or print(mysql_error());

mysql_query('CREATE TABLE Authors (
	Email VARCHAR(255) NOT NULL,
	VerifyDate DATETIME NOT NULL,
	VerifyCode VARBINARY(16),
	Session VARBINARY(40),
	PRIMARY KEY (Email)
) DEFAULT CHARSET=utf8')
 or print(mysql_error());

mysql_query('INSERT INTO Authors (Email,VerifyDate) VALUES (\''.mysql_real_escape_string(service_email).'\',NOW())')
 or print(mysql_error());

mysql_close();
