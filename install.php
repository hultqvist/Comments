<?php
// Create tables in database
// config.php must contain valid MySql connection information.

require_once("config.php");

//Uncomment line to reinstall table
//mysql_query('DROP TABLE Comments;') or print(mysql_error());
//mysql_query('DROP TABLE Links;') or print(mysql_error());
//mysql_query('DROP TABLE Sites;') or print(mysql_error());
//mysql_query('DROP TABLE Authors;') or print(mysql_error());

mysql_query('CREATE TABLE Comments (
	CommentID INT NOT NULL auto_increment,
	SiteID INT NOT NULL,
	Page VARCHAR(255) NOT NULL,
	PageUrl VARCHAR(255) NOT NULL,
	CommentIP VARBINARY(16) NOT NULL,
	CommentEmail VARCHAR(255) NOT NULL,
	CommentText VARCHAR(32767) NOT NULL,
	CommentDate DATETIME NOT NULL,
	VerifiedDate DATETIME,
	VerifiedIP VARBINARY(16),
	PRIMARY KEY (CommentID)
) DEFAULT CHARSET=utf8')
 or print('<div>'.mysql_error().'</div>');

mysql_query('CREATE TABLE Links (
	LinkID INT NOT NULL auto_increment,
	SiteID INT NOT NULL,
	Page VARCHAR(255) NOT NULL,
	VisitorIP VARBINARY(16) NOT NULL,
	Referer VARCHAR(255) NOT NULL,
	PRIMARY KEY (LinkID)
) DEFAULT CHARSET=utf8')
 or print('<div>'.mysql_error().'</div>');
//	CONSTRAINT uc_Links UNIQUE(SiteID,Page,VisitorIP,Referer)

mysql_query('CREATE TABLE Sites (
	SiteID INT NOT NULL auto_increment,
	SiteUrl VARCHAR(255) NOT NULL,
	AdminEmail VARCHAR(255),
	PRIMARY KEY (SiteID)
) DEFAULT CHARSET=utf8')
 or print('<div>'.mysql_error().'</div>');

mysql_query('INSERT INTO Sites (SiteID, SiteUrl,AdminEmail) VALUES (1, \''.mysql_real_escape_string(service_url).'\',\''.mysql_real_escape_string(service_email).'\')')
 or print('<div>'.mysql_error().'</div>');

mysql_query('CREATE TABLE Authors (
	Email VARCHAR(255) NOT NULL,
	VerifyDate DATETIME NOT NULL,
	VerifyCode VARBINARY(16),
	Session VARBINARY(40),
	PRIMARY KEY (Email)
) DEFAULT CHARSET=utf8')
 or print('<div>'.mysql_error().'</div>');

mysql_query('INSERT INTO Authors (Email,VerifyDate) VALUES (\''.mysql_real_escape_string(service_email).'\',NOW())')
 or print('<div>'.mysql_error().'</div>');

mysql_close();
?>

<h1>All done</h1>
<p>Now make sure that the <strong>inc/</strong> directory is writeable by the php script</p>
