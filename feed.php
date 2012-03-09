<?php
// Comment atom feed

header('Content-Type: application/atom+xml');
echo '<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
';

require_once("shared.php");
$site = GetSiteConstants($sid, false);
if(urlError)
{
	header("HTTP/1.1 404 Not Found");
	echo urlError;
	return;
}

// Read site data
$siteName = "Comment Feed";
$siteAuthor = "John Doe";

// Atom header

echo '
	<title>'.htmlentities($siteName).'</title>
	<link href="'.htmlentities($site['SiteUrl']).'"/>
	<link href="'.service_url.'/inc/'.$sid.'/'.urlencode($page).'.xml" rel="self"/>
	<updated>'.gmdate('Y-m-d\TH:i:s\Z').'</updated>
	<author>
		<name>'.htmlentities($siteAuthor).'</name>
	</author>
	<id>'.htmlentities($site['SiteUrl'].'/'.urlencode($page)).'</id>
';

// Read comments

$result = mysql_query('SELECT * FROM Comments
	WHERE SiteID = '.$sid.'
	AND Page = \''.mysql_real_escape_string($page).'\'
	AND VerifiedDate IS NOT NULL
	ORDER BY CommentDate DESC
')
 or die(mysql_error());

require_once('markdown.php');

while ($row = mysql_fetch_assoc($result)) {
	$link = $site['SiteUrl'].htmlentities($row['Page']).'#comment'.$row['CommentID'];
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
