<?php

require_once("../shared.php");

GetSessionConstants();
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
if(sessionEmail)
{
	echo '<h1>'.htmlentities(sessionEmail).' <a href="'.service_url.'/logout/">logout</a></h1>';

$result = @mysql_query('
	SELECT Comments.*, Sites.SiteUrl FROM Comments
	JOIN Sites on Comments.SiteID=Sites.SiteID
	WHERE CommentEmail=\''.mysql_real_escape_string(sessionEmail).'\'
')
 or die(mysql_error());

	require_once('../markdown.php');

	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		echo '<li id="comment'.$row['CommentID'].'">';
		echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon">';
		if($row['VerifiedDate'] === null)
		{
			echo '<strong>(unverified: '.htmlentities($row['CommentIP']).')</strong>';
			echo '<a href="'.service_url.'/verify/?cid='.$row['CommentID'].'">verify</a> ';
			echo '<a href="'.service_url.'/delete/?cid='.$row['CommentID'].'">delete</a> ';
		}
		echo '<span>'.$row['CommentDate'].'</span></div>';
		$url = htmlentities($row['SiteUrl'].$row['PagePath']);
		echo '<div><a href="'.$url.'">'.$url.'</a></div>';
		echo Markdown($row['CommentText']);
		echo '</li>';
	}
	echo '</ul>';


mysql_close();

}
else
{
?>
	<h1>Not logged in</h1>
	<form action="<?php echo service_url;?>/authorize" method="GET">
	<div>
		E-mail:
		<input type="text" name="email" />
		<input type="submit" value="Send me authorization link" />
	</div>
	</form>
<?php
}

?>
</article>
</body>
</html>
