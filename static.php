<?php
//Generates static js and html for comments

require_once('shared.php');

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

$path = trim($_SERVER["PATH_INFO"], '/');
$sla = strpos($path, '/');
$dot = strrpos($path, '.');
$sid = intval(substr($path, 0, $sla));
$page = substr($path, $sla + 1, $dot - $sla - 1);
$page = urldecode($page);
$type = substr($path, $dot + 1);

if($page.$type == "scriptjs"){
	header("Content-type: application/javascript");
	ob_start();
	require('script.php');
	$script = ob_get_contents();
	ob_flush();
	if(file_exists('inc/'.$sid) == false)
		mkdir('inc/'.$sid);
	//file_put_contents('inc/'.$sid.'/script.js', $script);
	exit;
}

//echo htmlentities("DEBUG>$page<DEBUG ".$_SERVER["PATH_INFO"], ENT_COMPAT, "UTF-8");

if($type == 'html'){
	//By reference redirect, not possible to make static
	if($page == 'ref')
	{
		//php version of transfering path into page
		//See script.php for js version
		$ref = $_SERVER['HTTP_REFERER'];
		$prot = strpos($ref, '://');
		if($prot !== FALSE)
			$ref = substr($ref, $prot + 3);
		$ref = trim(str_replace(array('/','?'), ' ', $ref));
		$ref = str_replace('  ', ' ', $ref);
		$ref = urlencode($ref);
		$ref = str_replace('+','%20',$ref);
		header('Location: '.service_url.'/inc/'.$sid.'/'.$ref.'.html');
		exit;
	}

	ob_start();
	require('comments.php');
	$size = ob_get_length();
	$html = ob_get_contents();
	//We want the browser to finish receiving data here and display it
	header("Content-Length: $size");
	ob_end_flush();

	if(file_exists('inc/'.$sid) == false)
		mkdir('inc/'.$sid);
	file_put_contents('inc/'.$sid.'/'.$page.'.html', $html);
	exit;
}

if($type == 'xml'){
	ob_start();
	require('feed.php');
	$xml = ob_get_contents();
	ob_flush();
	@mkdir('inc/'.$sid);
	file_put_contents('inc/'.$sid.'/'.$page.'.xml', $xml);
	exit;
}

die('Not yet implemented: '.$sid.' / '.$page.' . '.$type);
