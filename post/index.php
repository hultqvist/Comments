<?php
// This is called when comments get posted

header('Content-Type: text/html');

//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

require_once('../shared.php');
GetSiteConstants();

if(!isset($_REQUEST['ajax']))
	echo '<div><a href="'.service_url.'/comments/?sid='.siteID.'&url='.htmlentities(siteUrl.pagePath).'&form">back</a></div>';

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
GetSessionConstants();

//Save Comment
if(sessionEmail && $commentEmail === sessionEmail)
{
	//Already verified poster
	$res = @mysql_query('INSERT INTO Comments (SiteID, PagePath, CommentIP, CommentDate, CommentText, CommentEmail, VerifiedIP, VerifiedDate)
	VALUES
		('.siteID.',
		\''.mysql_real_escape_string(pagePath).'\',
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
	$res = @mysql_query('INSERT INTO Comments (SiteID, PagePath, CommentIP, CommentDate, CommentText, CommentEmail)
	VALUES
		('.siteID.',
		\''.mysql_real_escape_string(pagePath).'\',
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
			require_once('../shared.php');
			GenerateAndSendVerificationCode($commentEmail, siteUrl.pagePath);
		}
		echo '<div class="commentOk">Comment awaits your verification, check your email</div>';
	}
	else
		echo '<div class="commentOk">Comment awaits moderation</div>';
}

//Send email to site owner
$headers = "From: ".service_email."\nReply-To: ".$commentEmail;

mail(siteAdminEmail, "New comment on ".siteUrl.pagePath,
	"Dashboard: ".service_url."/dashboard/\n".
	"From: ".$_SERVER['REMOTE_ADDR']."\n".
	"Email: ".$commentEmail.($commentEmail == sessionEmail?'(verified)':'(not checked)')."\n".
	"To: ".siteUrl.pagePath."\n".
	$commentText, $headers);
