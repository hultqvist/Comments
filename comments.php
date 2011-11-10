<?php
// Present a single comment feed in raw html
// Used by script.php but can also be used directly

//Load $siteID and $siteUrl
require_once('parameters.php');

//Database and service parameters
require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());

// Read comments

$result = @mysql_query('
	SELECT * FROM comments
	WHERE SiteID = '.$siteID.'
	AND SiteUrl = \''.mysql_real_escape_string($siteUrl).'\'
	AND VerifiedDate IS NOT NULL
')
 or die(mysql_error());

//Feed icon
echo '<a href="'.$service_url.'/feed.php?sid='.$siteID.'&url='.urlencode($siteUrl).'">Comment feed</a>';

$count = mysql_num_rows($result);
if($count === 0)
	echo '<p>No comments</p>';
elseif($count === 1)
	echo '<p>One comment</p>';
else
	echo '<p>'.$count.' comments</p>';

require_once('markdown.php');

echo '<ul>';
while ($row = mysql_fetch_assoc($result)) {
	echo '<li id="comment'.$row['ID'].'">';
	echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon">';
	echo '<span>'.$row['CommentDate'].'</span></div>';
	$text = Markdown($row['CommentText']);
	$text = str_replace("\n", " ", $text);
	$text = str_replace("\r", " ", $text);
	$text = str_replace("'", "&apos;", $text);
	echo $text;
	echo '</li>';
}
echo '</ul>';

mysql_close();

// Comment Form

echo '<div id="commentForm">';
echo '<h1>Post your comment here</h1>';
echo '<form action="'.$service_url.'/script.php?sid='.$siteID.'&url='.urlencode($siteUrl).'" method="post" onsubmit="return commentPost();">';
echo '<textarea id="commentText" name="commentText"></textarea><br/>';
echo '<div id="commentStatus"></div>';
echo 'Your e-mail address for verification:<br/>';
echo '<input type="text" id="commentEmail" name="commentEmail" />';
echo '<input type="submit" value="Post comment" />';
echo '</form></div>';

