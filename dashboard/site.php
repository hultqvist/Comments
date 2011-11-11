<?php
	if(sessionEmail === null)
		return;

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
		echo '<li id="comment'.$row['CommentID'].'" class="'.($row['VerifiedDate'] === null?'unverified':'').'">';
		echo '<div class="commentAuthor"><img src="https://secure.gravatar.com/avatar/'.md5(strtolower(trim($row['CommentEmail']))).'?s=40&d=identicon"> ';
		if($row['CommentEmail'] === "")
			echo '<strong>Anonymous</strong> ';
		else
			echo htmlentities($row['CommentEmail']);
		echo '('.htmlentities($row['CommentIP']).') ';
		if($row['VerifiedDate'] === null)
		{
			echo '<strong>(unverified)</strong> ';
			echo '<a href="?verify='.$row['CommentID'].'">verify</a> ';
		}
		echo '<a href="?delete='.$row['CommentID'].'">delete</a> ';
		echo '<span>'.$row['CommentDate'].'</span></div>';
		$url = $siteUrl.htmlentities($row['PagePath']);
		echo '<div><a href="'.$url.'">'.$url.'</a></div>';
		echo Markdown($row['CommentText']);
		echo '</li>';
	}
	echo '</ul>';

