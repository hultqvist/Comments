<?php

require_once('parameters.php');

require_once("config.php");

mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_database) or die(mysql_error());
mysql_query("SET NAMES 'utf8'") or die(mysql_error());


//==== Comment List

$result = @mysql_query('
	SELECT * FROM comments
	WHERE SiteID = '.$siteID.'
	AND SiteUrl = \''.mysql_real_escape_string($siteUrl).'\'
	AND VerifiedDate IS NOT NULL
')
 or die(mysql_error());
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
	echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?s=40&d=identicon">';
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

//==== Comment Form

echo '<form action="'.$service_url.'/post.php?sid='.$siteID.'&url='.urlencode($siteurl).'" method="post" onsubmit="return commentPost();">';
echo '<textarea id="commentText" name="commentText"></textarea><br/>';
echo '<div id="commentStatus"></div>';
echo 'Your E-Mail address for verification:<br/>';
echo '<input type="text" id="commentEmail" name="commentEmail" />';
echo '<input type="submit" value="Post comment" />';
echo '</form>';

