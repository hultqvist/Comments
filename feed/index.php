<?php
// Comment atom feed

header('Content-Type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
';

require_once("../shared.php");
GetSiteConstants();
if(urlError)
{
	header("HTTP/1.1 404 Not Found");
	echo urlError;
	return;
}

// Read site data
$siteBaseUrl = siteUrl;
$siteName = "Comment Feed";
$siteAuthor = "John Doe";

// Atom header

echo '
	<title>'.htmlentities($siteName).'</title>
	<link href="'.htmlentities($siteBaseUrl).'"/>
	<link href="'.service_url.'/feed.php?sid='.siteID.'&amp;url='.urlencode(siteUrl.pagePath).'" rel="self"/>
	<updated>'.gmdate('Y-m-d\TH:i:s\Z').'</updated>
	<author>
		<name>'.htmlentities($siteAuthor).'</name>
	</author>
	<id>'.htmlentities(siteUrl.pagePath).'</id>
';

// Read comments

$result = @mysql_query('
	SELECT * FROM Comments
	WHERE SiteID = '.siteID.'
	AND PagePath = \''.mysql_real_escape_string(pagePath).'\'').'
	AND VerifiedDate IS NOT NULL
	ORDER BY CommentDate DESC
	LIMIT 50
')
 or die(mysql_error());

require_once('../markdown.php');

while ($row = mysql_fetch_assoc($result)) {
	$link = siteUrl.htmlentities($row['PagePath']).'#comment'.$row['CommentID'];
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
