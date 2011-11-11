<?php

require_once("../shared.php");

GetSessionConstants();

if(!sessionEmail)
{
	header('Location: '.service_url.'/dashboard/');
	return;
}
?>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment Dashboard</title>
	<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<article>
<?php
	echo '<h1>'.htmlentities(sessionEmail).' <a href="'.service_url.'/logout/">logout</a></h1>';

	$sid=intval($_GET['sid']);

	//Sites
	$result = @mysql_query('SELECT * FROM Sites WHERE AdminEmail=\''.mysql_real_escape_string(sessionEmail).'\' AND SiteID='.$sid)
	 or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if(!$row) {
		echo 'No site with sid='.$sid;
		return;
	}

	$siteUrl = htmlentities($row['SiteUrl']);
	echo '<a href="'.$siteUrl.'">'.$siteUrl.'</a>';

	//Comments
	$result = @mysql_query('
		SELECT * FROM Comments
		WHERE SiteID='.$sid)
	or die(mysql_error());

	require_once('../markdown.php');

	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		echo '<li id="comment'.$row['CommentID'].'">';
		echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon">';
		if($row['VerifiedDate'] === null)
		{
			echo '<strong>(unverified: '.htmlentities($row['CommentIP']).')</strong> ';
			echo '<a href="'.service_url.'/verify/?cid='.$row['CommentID'].'">verify</a> ';
			echo '<a href="'.service_url.'/delete/?cid='.$row['CommentID'].'">delete</a> ';
		}
		echo '<span>'.$row['CommentDate'].'</span></div>';
		$url = $siteUrl.htmlentities($row['PagePath']);
		echo '<div><a href="'.$url.'">'.$url.'</a></div>';
		echo Markdown($row['CommentText']);
		echo '</li>';
	}
	echo '</ul>';

	mysql_close();
?>
</article>
</body>
</html>
