<?php

//Database and service parameters
require_once("config.php");

function PrintComment($site, $row, $session = false)
{
	if($row['VerifiedDate'] === null)
		echo '<li id="comment'.$row['CommentID'].'" class="unverified">';
	else
		echo '<li id="comment'.$row['CommentID'].'">';
	echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon">';
	if(isset($row['Page']))
	{
		$url=htmlentities($site['SiteUrl'].$row['Page']);
		echo '<div><a href="'.$url.'">'.$url.'</a></div>';
	}
	echo '<span>'.date('Y-m-d H:i', strtotime($row['CommentDate'])).'</span> ';

	if($session && ($session['Email'] === $site['AdminEmail'] || $session['Email'] === $row['CommentEmail']))
	{
		if($row['CommentEmail'] === "")
			echo '<strong>Anonymous</strong>';
		elseif($session['Email'] != $row['CommentEmail'])
			echo htmlentities($row['CommentEmail']);
		if($row['VerifiedDate'] === null)
		{
			echo ' <em>('.htmlentities($row['CommentIP']).')</em>';
			echo ' <strong>(unverified)</strong>';
			echo ' <a href="'.service_url.'/dashboard/verify.php?verify='.$row['CommentID'].'">verify</a>';
			echo ' <a href="'.service_url.'/dashboard/delete.php?delete='.$row['CommentID'].'">delete</a>';
		}
		elseif($session['Email'] === $site['AdminEmail'])
			echo ' <a href="'.service_url.'/dashboard/delete.php?delete='.$row['CommentID'].'">delete</a>';
	}

	echo '</div>';
	echo Markdown($row['CommentText']);
	echo '</li>';
}

function PrintLink($site, $row, $session = false)
{
	echo '<li class="unverified">';
	echo '<div class="commentAuthor">';
	$url=htmlentities($row['Referer']);
	echo '<div>Referer: <a href="'.$url.'">'.$url.'</a></div>';

	$url=htmlentities($site['SiteUrl'].$row['Page']);
	echo '<div>Page: <a href="'.$url.'">'.$url.'</a></div>';

	if($session && $session['Email'] === $site['AdminEmail'])
	{
		echo ' <a href="'.service_url.'/dashboard/verify.php?verify='.$row['LinkID'].'">verify</a>';
		echo ' <a href="'.service_url.'/dashboard/delete.php?delete='.$row['LinkID'].'">delete</a>';
	}

	echo '</div>';
	echo '<p>Unique IP: '.htmlentities($row['Count']).'</p>';
	echo '</li>';
}

//Page URL checks and normalization
function GetSiteConstants($sid, $checkReferer = TRUE)
{
	//Page URL
	if($checkReferer){
		if(isset($_SERVER['HTTP_REFERER']))
			$url = $_SERVER['HTTP_REFERER'];
		else
		{
			define("urlError", 'Missing referer');
			return;
		}
		if(isset($_GET['url']))
			$url = $_GET['url'];
		if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)
		{
			define("urlError", 'Invalid url: '.htmlentities($url));
			return;
		}
	}

	//Verify pageUrl and referer
	$res = @mysql_query('SELECT * FROM Sites WHERE SiteID='.$sid)
		or die(mysql_error());
	$row = mysql_fetch_assoc($res);
	if($row === FALSE)
	{
		define("urlError", 'No site with sid: '.$sid);
		return;
	}
	$siteUrl = parse_url(rtrim($row['SiteUrl'], "/"));
	if($checkReferer){
		$pageUrl = parse_url(rtrim($url, "/"));
		if($pageUrl['host'] != $siteUrl['host'] || strpos($pageUrl['path'], $siteUrl['path']) !== 0)
		{
			define("urlError", 'Wrong url of page: '.htmlentities($url).' expected: '.htmlentities($row['SiteUrl']));
			return;
		}
		$serviceUrl = parse_url(service_url);
		$refUrl = parse_url($_SERVER['HTTP_REFERER']);
		if($refUrl['host'] != $serviceUrl['host'] || strpos($refUrl['path'], $serviceUrl['path']) !== 0)
		{
			if($refUrl['host'] != $siteUrl['host'] || strpos($refUrl['path'], $siteUrl['path']) !== 0)
			{
				define("urlError", 'Wrong referer: '.htmlentities($_SERVER['HTTP_REFERER']).' expected: '.htmlentities($row['SiteUrl']));
				return;
			}
		}
	}
	define("urlError", FALSE);
	return $row;
}



function GenerateAndSendVerificationCode($email, $url)
{
	$code = substr(sha1(time().rand().$email.$_SERVER['REMOTE_ADDR']), 0, 10);
	@mysql_query('REPLACE Authors SET Email=\''.mysql_real_escape_String($email).'\', VerifyDate=NOW(), VerifyCode=\''.mysql_real_escape_String($code).'\'')
	 or die('<div class="commentError">'.mysql_error().'</div>');

	//Email verification link
	$mailed = mail($email,
		'Verify your comment',
		'To verify the comment you made on '.$url.'

Click here to login and review your comments:
'.service_url.'/authorize/?email='.urlencode($email).'&code='.$code,
		'From: '.service_email)
	or die('<div class="commentError">Failed to send verification email, try again</div>');
}

//Return the email address verified by session cookie
function GetSessionConstants()
{
	$email = isset($_COOKIE['email'])? $_COOKIE['email'] : null;
	$session = isset($_COOKIE['session'])? $_COOKIE['session'] : null;
	$res = @mysql_query('SELECT * FROM Authors
		WHERE Email=\''.mysql_real_escape_string($email).'\'
		AND Session=\''.mysql_real_escape_string($session).'\'')
		or die('<div class="commentError">'.mysql_error().'</div>');
	if(mysql_num_rows($res) !== 1)
	{
		setcookie("session", null, time()-24*3600);
		return NULL;
	}
	return mysql_fetch_assoc($res);
}



function LogReferer($site, $page)
{
	if(!isset($_GET['ref']))
		return;
	$ref = $_GET['ref'];
	if(filter_var($ref, FILTER_VALIDATE_URL) === FALSE)
		return;
	if(strpos($ref, $site['SiteUrl']) !== false) //Don't log internal links
		return;
	$res = @mysql_query('INSERT INTO Links (SiteID, Page, VisitorIP, Referer)
	VALUES
		('.$sid.',
		\''.mysql_real_escape_string($page).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		\''.mysql_real_escape_string($ref).'\'
	)')
	or die('<div class="commentError">'.mysql_error().'</div>');
}
