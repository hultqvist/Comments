<?php
// This is called when comments get posted

header('Content-Type: text/html');

//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

if(isset($_GET['sid']))
	$sid = intval($_GET['sid']);
else
	$sid = 0;
if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = null;

require_once('shared.php');
$site = GetSiteConstants($sid);

if(!isset($_REQUEST['ajax']))
	echo '<div><a href="'.service_url.'/inc/'.$sid.'/'.urlencode($page).'.html">back</a></div>';

if(urlError)
{
	echo '<div class="commentError">'.urlError.'</div>';
	return;
}

$commentText = trim($_POST['commentText']);
$commentEmail = filter_var($_POST['commentEmail'], FILTER_SANITIZE_EMAIL);
$commentEmail = strtolower($commentEmail);

//Verify input
if(strlen($commentText) === 0)
{
	echo '<div class="commentError">Empty text</div>';
	return;
}
if($commentEmail != "" && filter_var($commentEmail, FILTER_VALIDATE_EMAIL) === FALSE)
{
	echo '<div class="commentError">Invalid email address</div>';
	return;
}

//Get poster session
$session = GetSessionConstants();

//Save Comment
if($session && $commentEmail === $session['Email'])
{
	//Already verified poster
	$res = @mysql_query('INSERT INTO Comments (SiteID, Page, PageUrl, CommentIP, CommentDate, CommentText, CommentEmail, VerifiedIP, VerifiedDate)
	VALUES
		('.$sid.',
		\''.mysql_real_escape_string($page).'\',
		\''.mysql_real_escape_string($_SERVER['HTTP_REFERER']).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		NOW(),
		\''.mysql_real_escape_string($commentText).'\',
		\''.mysql_real_escape_string($commentEmail).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		NOW()
	)')
	or die('<div class="commentError">'.mysql_error().'</div>');

	echo '<div class="commentOk">Comment posted.</div>';
}
else
{
	//Non verified comment
	$res = @mysql_query('INSERT INTO Comments (SiteID, Page, PageUrl, CommentIP, CommentDate, CommentText, CommentEmail)
	VALUES
		('.$sid.',
		\''.mysql_real_escape_string($page).'\',
		\''.mysql_real_escape_string($_SERVER['HTTP_REFERER']).'\',
		\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		NOW(),
		\''.mysql_real_escape_string($commentText).'\',
		\''.mysql_real_escape_string($commentEmail).'\'
	)')
	or die('<div class="commentError">'.mysql_error().'</div>');


	$id = mysql_insert_id();

	if($commentEmail)
	{
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
			GenerateAndSendVerificationCode($commentEmail, $site['SiteUrl'].$page);
		}
		echo '<div class="commentOk">Comment awaits your verification, check your email</div>';
	}
	else
		echo '<div class="commentOk">Comment awaits moderation</div>';
}

//Send email to site owner
$headers = "From: ".service_email."\nReply-To: ".$commentEmail;

mail($site['AdminEmail'], "New comment on ".$site['SiteUrl'].' '.$page,
	"Dashboard: ".service_url."/dashboard/\n".
	"Referrer: ".$_SERVER['HTTP_REFERER']."\n".
	"From: ".$_SERVER['REMOTE_ADDR']."\n".
	"Email: ".$commentEmail.($commentEmail == $session['Email']?'(verified)':'(not checked)')."\n".
	"To: ".$site['SiteUrl'].' '.$page."\n".
	$commentText, $headers);
