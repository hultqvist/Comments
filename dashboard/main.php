<?php
	//Sites
	echo '<h1>Your sites</h1>';
	$result = @mysql_query('SELECT * FROM Sites WHERE AdminEmail=\''.mysql_real_escape_string(sessionEmail).'\'')
	 or die(mysql_error());
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		echo '<li>';
		echo '<a href="?sid='.$row['SiteID'].'">'.htmlentities($row['SiteUrl']).'</a>';
		echo '</li>';
	}
	echo '</ul>';

	//Comments
	echo '<h1>Your comments</h1>';
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
