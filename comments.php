<?php
	$siteID = intval($_GET['sid']);
	if(isset($_GET['url']))
		$siteUrl = $_GET['url'];
	else
		$siteUrl = $_SERVER['HTTP_REFERER'];

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
	echo '<li><a name="comment'.$row['ID'].'">';
	echo '<p>At '.$row['CommentDate'].'</p>';
	$text = Markdown($row['CommentText']);
	$text = str_replace("\n", " ", $text);
	$text = str_replace("\r", " ", $text);
	$text = str_replace("'", "&apos;", $text);
	echo '<p>'.$text.'</p>';
	echo '</li>';
}
echo '</ul>';

mysql_close();

//==== Comment Form

echo '<form action="'.$service_url.'/post.php?id='.$siteID.'&url='.urlencode($siteurl).'" method="post" onsubmit="return commentPost();">';
echo '<textarea id="commentText" style="width:80%; height: 12em"></textarea><br/>';
echo '<div id="commentStatus"></div>';
echo 'Your E-Mail address for verification:<br/>';
echo '<input style="width:50%; margin-right: 10%;" type="text" id="commentEmail" />';
echo '<input style="width:20%" type="submit" value="Send" />';
echo '</form>';

