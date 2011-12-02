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
$query='SELECT * FROM Comments
	WHERE SiteID = '.siteID.'
	AND PagePath = \''.mysql_real_escape_string(pagePath).'\'';
if(!sessionEmail || sessionEmail !== siteAdminEmail)
	$query .= ' AND VerifiedDate IS NOT NULL';
$result = @mysql_query($query) or die(mysql_error());

//Style
echo '<style type="text/css">';
require('style.css');
echo '</style>';

//Feed icon
echo '<div class="commentFeed"><a href="'.service_url.'/feed/?sid='.siteID.'&url='.urlencode(siteUrl.pagePath).'"><img src="'.service_url.'/feed.png" /></a></div>';

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
		PrintComment($row);
	}
	echo '</ul>';

mysql_close();

// Comment Form
if(isset($_GET['form'])){?>
<form id="commentForm" action="<?php echo service_url.'/post/?sid='.siteID.'&url='.urlencode(siteUrl.pagePath);?>" method="post" onsubmit="return commentPost();">
	<textarea id="commentText" name="commentText" required></textarea><br/>
	<div>Your e-mail address for verification:<?php
	if(sessionEmail)
	{
		echo ' <a href="'.service_url.'/dashboard/">dashboard</a>';
		echo ' <a href="'.service_url.'/logout/">logout</a>';
	}
?></div>
	<input type="email" id="commentEmail" name="commentEmail" placeholder="e-mail to verify comment" value="<?php
		if(isset($_COOKIE['email']))
			echo $_COOKIE['email']; ?>"/>
	<input type="submit" value="Post comment" />
	<div id="commentStatus"></div>
</form>
<?php
}
