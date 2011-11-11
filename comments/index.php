<?php
// Present a single comment feed in raw html
// Used by script.php but can also be used directly

//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

require_once('../shared.php');
GetSiteConstants();

if(urlError)
{
	echo '<div class="commentError">'.urlError.'</div>';
	return;
}

GetSessionConstants();

// Read comments
$result = @mysql_query('
	SELECT * FROM Comments
	WHERE SiteID = '.siteID.'
	AND PagePath = \''.mysql_real_escape_string(pagePath).'\'
	AND VerifiedDate IS NOT NULL
')
 or die(mysql_error());

//Style
echo '<style type="text/css">';
require('style.css');
echo '</style>';

//Feed icon
echo '<a href="'.service_url.'/feed/?sid='.siteID.'&url='.urlencode(siteUrl.pagePath).'">Comment feed</a>';

	$count = mysql_num_rows($result);
	if($count === 0)
		echo '<p>No comments</p>';
	elseif($count === 1)
		echo '<p>One comment</p>';
	else
		echo '<p>'.$count.' comments</p>';

	require_once('../markdown.php');

	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		echo '<li id="comment'.$row['CommentID'].'">';
		echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon">';
		echo '<span>'.date('Y-m-d H:i', strtotime($row['CommentDate'])).'</span></div>';
		echo Markdown($row['CommentText']);
		echo '</li>';
	}
	echo '</ul>';

mysql_close();

// Comment Form
if(isset($_GET['form'])){?>
<div id="commentForm">
	<h1>Post your comment here</h1>
	<form action="<?php echo service_url.'/script.php?sid='.siteID.'&url='.urlencode(siteUrl.pagePath);?>" method="post" onsubmit="return commentPost();">
	<textarea id="commentText" name="commentText"></textarea><br/>
	<div id="commentStatus"></div>
	<div>Your e-mail address for verification:<?php
	if(sessionEmail)
	{
		echo ' <a href="'.service_url.'/dashboard/">dashboard</a>';
		echo ' <a href="'.service_url.'/logout/">logout</a>';
	}
?></div>
	<input type="text" id="commentEmail" name="commentEmail" value="<?php
		if(isset($_COOKIE['email']))
			echo $_COOKIE['email']; ?>"/>
	<input type="submit" value="Post comment" />
	</form>
</div>
<?php
}
