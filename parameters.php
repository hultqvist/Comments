<?php
// Read $siteID and $siteUrl
// also do some normalization on $sitUrl

$siteID = intval($_GET['sid']);

$siteUrl = FALSE;
if(isset($_GET['url']))
	$siteUrl = $_GET['url'];
else if(isset($_SERVER['HTTP_REFERER']))
	$siteUrl = $_SERVER['HTTP_REFERER'];
if(filter_var($siteUrl, FILTER_VALIDATE_URL) === FALSE)
	$siteUrl = FALSE;
