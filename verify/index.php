<?php
// Linked from dashboard for user to verify a comment
// Parameters:
//	GET: cid = commentid
//	COOKIE: email and session
header('Content-Type: text/html');

require_once('../shared.php');

//Get poster session
GetSessionConstants();

$cid=intval($_GET['cid']);

//Verify as poster
$res = @mysql_query('UPDATE Comments
	SET
	VerifiedIP=\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
	VerifiedDate=NOW()
	WHERE CommentID='.$cid.'
	AND CommentEmail=\''.mysql_real_escape_string(sessionEmail).'\'
	AND VerifiedIP IS NULL
')
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_affected_rows() === 1)
{
	header('Location: '.service_url.'/dashboard/');
	return;
}

//Verify as site admin
$res = mysql_query('
	SELECT Sites.AdminEmail, Sites.SiteID
	FROM Sites
	JOIN Comments ON Comments.SiteID=Sites.SiteID
	WHERE Comments.CommentID='.$cid)
or die('<div class="commentError">'.mysql_error().'</div>');
$row = mysql_fetch_assoc($res);
if(!$row)
	die('<div class="commentError">No comment found.</div>');
if($row['AdminEmail'] != sessionEmail)
	die('<div class="commentError">No comment found.</div>');

$res = @mysql_query('UPDATE Comments
	SET
	VerifiedIP=\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
	VerifiedDate=NOW(),
	CommentEmail=\'\'
	WHERE CommentID='.$cid.'
	AND VerifiedIP IS NULL
')
or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_affected_rows() === 1)
{
	header('Location: '.service_url.'/dashboard/site.php?sid='.intval($row['SiteID']));
	return;
}
die('<div class="commentError">No comment found.</div>');


