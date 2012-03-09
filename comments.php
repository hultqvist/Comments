<?php
// Present a single comment feed in raw html
// Used by script.php but can also be used directly

//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

if(!isset($sid)) die('Missing sid');

require_once('shared.php');
$site = GetSiteConstants($sid, false);

if(urlError)
{
	echo '<div class="commentError">'.urlError.'</div>';
	return;
}

$session = GetSessionConstants();

// Read comments
$query='SELECT * FROM Comments
 WHERE SiteID = '.$sid.' AND Page = \''.mysql_real_escape_string($page).'\'
 AND VerifiedDate IS NOT NULL';
$result = @mysql_query($query) or die(mysql_error());

//Style
echo '<style type="text/css">';
require('comments.css');
echo '</style>';

echo htmlentities("DEBUG>$page<DEBUG");
//Feed icon
echo '<div class="commentFeed"><a href="'.service_url.'/inc/'.$sid.'/'.urlencode($page).'.xml"><img src="'.service_url.'/feed.png" /></a></div>';

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
		PrintComment($site, $row);
	}
	echo '</ul>';

// Comment Form
?>
<form id="commentForm" action="<?php echo service_url.'/post.php?sid='.$sid.'&page='.urlencode($page);?>" method="post" onsubmit="return commentPost();">
	<textarea id="commentText" name="commentText" required></textarea><br/>
	<div>Your e-mail address for verification:<?php
	if($session)
	{
		echo ' <a href="'.service_url.'/dashboard/">dashboard</a>';
		echo ' <a href="'.service_url.'/logout.php">logout</a>';
	}
?></div>
	<input type="email" id="commentEmail" name="commentEmail" placeholder="e-mail to verify comment" value="<?php
		if(isset($_COOKIE['email']))
			echo $_COOKIE['email']; ?>"/>
	<input type="submit" value="Post comment" />
	<div id="commentStatus"></div>
</form>
