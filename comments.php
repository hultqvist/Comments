<?php
// Present a single comment feed in raw html
// Used by script.php but can also be used directly

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
 AND VerifiedDate IS NOT NULL
 ORDER BY CommentDate ASC';
$result = @mysql_query($query) or die(mysql_error());

//Style
echo '<style type="text/css">';
require('comments.css');
echo '</style>';

//Feed icon
echo '<div class="commentFeed"><a href="'.service_url.'/inc/'.$sid.'/'.str_replace('+','%20',urlencode($page)).'.xml"><img src="'.service_url.'/feed.png" /></a></div>';

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
	<textarea id="commentText" name="commentText" required></textarea>
	<div><a href="http://daringfireball.net/projects/markdown/" target="_new">Markdown syntax</a></div>
	<div id="commentPreview">Preview</div>
	<div>Your e-mail address for verification:
		<span id="commentDash">
			<a href="<?php echo service_url;?>/dashboard/" target="_new">dashboard</a>
			<a id="commentLogout" href="<?php echo service_url;?>/auth.php?logout">logout</a>
		</span>
	</div>
	<input type="email" id="commentEmail" name="commentEmail" placeholder="E-mail to verify comment" value=""/>
	<input type="submit" value="Post comment" />
	<div id="commentStatus"></div>
</form>
