<?php
// Read $siteID and $pageUrl
// also do some normalization on $sitUrl

$siteID = intval($_GET['sid']);

$pageUrl = FALSE;
if(isset($_GET['url']))
	$pageUrl = $_GET['url'];
else if(isset($_SERVER['HTTP_REFERER']))
	$pageUrl = $_SERVER['HTTP_REFERER'];
if(filter_var($pageUrl, FILTER_VALIDATE_URL) === FALSE)
	$pageUrl = FALSE;
