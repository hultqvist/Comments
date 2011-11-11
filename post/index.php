<?php
// This is called when comments get posted

header('Content-Type: text/html');

//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

//Load $siteID and $pageUrl
require_once('../parameters.php');

$commentText = trim($_POST['commentText']);
$commentEmail = filter_var($_POST['commentEmail'], FILTER_SANITIZE_EMAIL);
$commentEmail = strtolower($commentEmail);

//Verify input
if(strlen($commentText) === 0)
{
	echo '<div class="commentError">Empty text</div>';
	return;
}
if(filter_var($commentEmail, FILTER_VALIDATE_EMAIL) === FALSE)
{
	echo '<div class="commentError">Invalid email address</div>';
	return;
}
if($pageUrl === FALSE)
{
	echo '<div class="commentError">Invalid site url: '.htmlentities($_GET['url']).', contact website owner</div>';
	return;
}

require_once('../shared.php');

//Verify pageUrl
$res = @mysql_query('SELECT SiteUrl FROM Sites WHERE SiteID='.$siteID)
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_num_rows($res) !== 1)
	die('<div class="commentError">No site with sid: '.$siteID.'</div>');
$row = mysql_fetch_assoc($res);
if(strpos($pageUrl, $row['SiteUrl']) !== 0)
{
	echo '<div class="commentError">Wrong url of page: '.htmlentities($pageUrl).' expected: '.htmlentities($row['SiteUrl']).'</div>';
	return;
}

//Get poster session
$sessionEmail = GetSessionEmail();

//Save Comment
if($commentEmail == $sessionEmail)
{
	$res = @mysql_query('INSERT INTO Comments (SiteID, PageUrl, CommentIP, CommentDate, CommentText, CommentEmail, VerifiedIP, VerifiedDate)
	VALUES
		('.$siteID.',
		\''.mysql_real_escape_string($pageUrl).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		NOW(),
		\''.mysql_real_escape_string($commentText).'\',
		\''.mysql_real_escape_string($commentEmail).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		NOW()
	)')
	or die('<div class="commentError">'.mysql_error().'</div>');

	echo '<div class="commentOk">Comment posted.</div>';
	return;
}

//Non verified comment
$res = @mysql_query('INSERT INTO Comments (SiteID, PageUrl, CommentIP, CommentDate, CommentText, CommentEmail)
VALUES
	('.$siteID.',
	\''.mysql_real_escape_string($pageUrl).'\',
	\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
	NOW(),
	\''.mysql_real_escape_string($commentText).'\',
	\''.mysql_real_escape_string($commentEmail).'\'
)');

$id = mysql_insert_id();

//Get Author
$verificationCode = TRUE;
$res = @mysql_query('SELECT * FROM Authors WHERE Email=\''.mysql_real_escape_String($commentEmail).'\'')
	or die('<div class="commentError">'.mysql_error().'</div>');
$row = mysql_fetch_assoc($res);
if($row)
{
	//Limit one verification email per day, unless already verified
	if($row['VerifyCode'] !== NULL)
	{
		$vd = strtotime($row['VerifyDate']);
		if($vd < time() + 3600*24)
		{
			echo '<div class="commentOk">Email verification already sent.</div>';
			$verificationCode = FALSE;
		}
	}
}

//Create new VerifyCode
if($verificationCode === TRUE)
{
	require_once('../shared.php');
	GenerateAndSendVerificationCode($commentEmail, $pageUrl);
}
echo '<div class="commentOk">Comment awaits your verification, check your email</div>';
