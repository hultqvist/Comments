<?php
$cid=intval($_GET['delete']);

//Delete as poster
$res = @mysql_query('DELETE FROM Comments
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

//Delete as site admin
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

$res = @mysql_query('DELETE FROM Comments WHERE CommentID='.$cid)
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_affected_rows() === 1)
{
	header('Location: '.service_url.'/dashboard/?sid='.intval($row['SiteID']));
	return;
}
die('<div class="commentError">No comment found.</div>');


