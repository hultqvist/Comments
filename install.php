<?php
// Create tables in database
// config.php must contain valid MySql connection information.

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'")
 or die(mysql_error());

mysql_query('DROP TABLE Comments;') or print(mysql_error());
mysql_query('DROP TABLE Sites;') or print(mysql_error());
mysql_query('DROP TABLE Authors;') or print(mysql_error());

mysql_query('CREATE TABLE Comments (
	CommentID INT NOT NULL auto_increment,
	SiteID INT NOT NULL,
	PageUrl TINYTEXT NOT NULL,
	CommentIP TINYTEXT,
	CommentText text NOT NULL,
	CommentDate DATETIME NOT NULL,
	CommentEmail TINYTEXT,
	VerificationCode TINYBLOB,
	VerifiedDate DATETIME,
	VerifiedIP TINYTEXT,
	PRIMARY KEY (CommentID)
)')
 or print(mysql_error());

mysql_query('CREATE TABLE Sites (
	SiteID INT NOT NULL auto_increment,
	SiteUrl TINYTEXT NOT NULL,
	AdminAuthorID INT,
	PRIMARY KEY (SiteID)
)')
 or print(mysql_error());

mysql_query('CREATE TABLE Authors (
	AuthorID INT NOT NULL auto_increment,
	AuthorEmail TINYTEXT NOT NULL,
	AuthorCode TINYBLOB,
	PRIMARY KEY (AuthorID)
)')
 or print(mysql_error());

mysql_close();
