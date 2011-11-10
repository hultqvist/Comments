<?php
// Read $siteID and $siteUrl
// also do some normalization on $sitUrl

$siteID = intval($_GET['sid']);

$siteUrl = $_SERVER['HTTP_REFERER'];
if(isset($_GET['url']) && filter_var($_GET['url'], FILTER_VALIDATE_URL))
	$siteUrl = $_GET['url'];
if($siteUrl == ""){
	$siteUrl = FALSE;
}else{
	//Remove http(s)://
	$pos = strpos($siteUrl, "://");
	if($pos > 0)
		$siteUrl = substr($siteUrl, strpos($siteUrl, "://") + 3);
	// Lowercase
	$siteUrl = strtolower($siteUrl);
}
