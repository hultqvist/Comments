<?php
// Comment atom feed

header('Content-Type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
';

//Load $siteID and $pageUrl
require_once('../parameters.php');

//Database and service parameters
require_once("../shared.php");

// Read site data
$siteBaseUrl = service_url;
$siteName = "Comment Feed";
$siteAuthor = "John Doe";
$res = @mysql_query('SELECT SiteUrl FROM Sites WHERE SiteID='.$siteID)
	or die('<div class="commentError">'.mysql_error().'</div>');
if(mysql_num_rows($res) !== 1)
	die('<div class="commentError">No site with sid: '.$siteID.'</div>');
$row = mysql_fetch_assoc($res);
if($row)
	$siteBaseUrl = $row['SiteUrl'];

// Atom header

echo '
	<title>'.htmlentities($siteName).'</title>
	<link href="'.htmlentities($siteBaseUrl).'"/>
	<link href="'.service_url.'/feed.php?sid='.$siteID.'&amp;url='.urlencode($pageUrl).'" rel="self"/>
	<updated>'.gmdate('Y-m-d\TH:i:s\Z').'</updated>
	<author>
		<name>'.htmlentities($siteAuthor).'</name>
	</author>
	<id>'.htmlentities($pageUrl).'</id>
';

// Read comments

$result = @mysql_query('
	SELECT * FROM Comments
	WHERE SiteID = '.$siteID.'
	'.($pageUrl === FALSE ? '' : 'AND pageUrl = \''.mysql_real_escape_string($pageUrl).'\'').'
	AND VerifiedDate IS NOT NULL
	ORDER BY CommentDate DESC
	LIMIT 50
')
 or die(mysql_error());

require_once('../markdown.php');

while ($row = mysql_fetch_assoc($result)) {
	$link = htmlentities($row['PageUrl']).'#comment'.$row['CommentID'];
	echo '
	<entry>
		<title>Comment</title>
		<link href="'.$link.'" />
		<id>'.$link.'</id>
		<updated>'.gmdate('Y-m-d\TH:i:s\Z', strtotime($row['CommentDate'])).'</updated>
		<content type="html">'.htmlentities(Markdown($row['CommentText'])).'</content>
	</entry>
';
}
mysql_close();

echo '</feed>';
