<?php
require_once("shared.php");
$session = GetSessionConstants() or die('No session');

$cid=intval($_GET['cid']);
$action=$_GET['action'];

//Get comment to do the action on
$res = @mysql_query('SELECT * FROM Comments WHERE CommentID='.$cid)
	or die('<div class="commentError">'.mysql_error().'</div>');
$c = mysql_fetch_assoc($res)
	or die('<div class="commentError">No comment with id '.$cid.'</div>');

//DELETE
if($action === 'delete'){
	
	//Delete unverified comment as poster
	$res = @mysql_query('DELETE FROM Comments
		WHERE CommentID='.$cid.'
		AND CommentEmail=\''.mysql_real_escape_string($session['Email']).'\'
		AND VerifiedIP IS NULL
	')
		or die('<div class="commentError">'.mysql_error().'</div>');
	if(mysql_affected_rows() === 1)
	{
		//no need to update since the comment was not verified before, hence not visible
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
	$row = mysql_fetch_assoc($res)
		or die('<div class="commentError">No comment found.</div>');
	if($row['AdminEmail'] != $session['Email'])
		die('<div class="commentError">No comment found.</div>');

	$res = @mysql_query('DELETE FROM Comments WHERE CommentID='.$cid)
		or die('<div class="commentError">'.mysql_error().'</div>');
	if(mysql_affected_rows() === 1)
	{
		if($c['VerifiedIP']) //update if comment is removed
			UpdateComments($c['SiteID'], $c['Page']);
		header('Location: '.service_url.'/dashboard/?sid='.intval($row['SiteID']));
		return;
	}
	die('<div class="commentError">No comment found.</div>');
}

//Verify
if($action === 'verify'){

	if($c['VerifiedIP'])
		die('<div class="commentError">Already verified</div>');

	//Verify as poster
	$res = @mysql_query('UPDATE Comments
		SET
		VerifiedIP=\''.mysql_real_escape_string($_SERVER['REMOTE_ADDR']).'\',
		VerifiedDate=NOW()
		WHERE CommentID='.$cid.'
		AND CommentEmail=\''.mysql_real_escape_string($session['Email']).'\'
		AND VerifiedIP IS NULL
	')
		or die('<div class="commentError">'.mysql_error().'</div>');
	if(mysql_affected_rows() === 1)
	{
		UpdateComments($c['SiteID'], $c['Page']);
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
	if($row['AdminEmail'] != $session['Email'])
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
		UpdateComments($c['SiteID'], $c['Page']);
		header('Location: '.service_url.'/dashboard/?sid='.intval($row['SiteID']));
		return;
	}
	die('<div class="commentError">No comment found.</div>');
}

die('<div class="commentError">Unknown action.</div>');
