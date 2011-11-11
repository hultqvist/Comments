<?php
// Linked from dashboard for user to verify a comment
// Parameters:
//	GET: cid = commentid
//	COOKIE: email and session
header('Content-Type: text/html');

require_once('../shared.php');

//Get poster session
$sessionEmail = GetSessionEmail();

$cid=intval($_GET['cid']);

$res = @mysql_query('UPDATE Comments
	SET
	VerifiedIP=\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
	VerifiedDate=NOW()
	WHERE CommentID='.$cid.'
	AND CommentEmail=\''.mysql_real_escape_string(sessionEmail).'\'
	AND VerifiedIP IS NULL
')
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_num_rows($res) === 0)
	die('<div class="commentError">No comment found to verify</div>');

header('Location: '.service_url.'/dashboard/');

