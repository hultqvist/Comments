<?php

// Instructions:
// 1. Adjust all fields
// 2. Rename this file to config.php

//MySql connection parameters
mysql_connect("localhost", "username", "password");
mysql_select_db("database") or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

//Base url for the service
define("service_url", "http://example.com/comments");
//From address in verification emails
define("service_email", "webmaster@example.com");

