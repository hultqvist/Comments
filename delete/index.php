<?php
// Linked from dashboard for user to delete an unverified comment
// Parameters:
//	GET: cid = commentid
//	COOKIE: email and session
header('Content-Type: text/html');

require_once('../shared.php');

GetSessionConstants();

$cid=intval($_GET['cid']);

$res = @mysql_query('DELETE FROM Comments
	WHERE CommentID='.$cid.'
	AND CommentEmail=\''.mysql_real_escape_string(sessionEmail).'\'
	AND VerifiedIP IS NULL
')
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_num_rows($res) === 0)
	die('<div class="commentError">No comment found to delete</div>');

header('Location: '.service_url.'/dashboard/');

