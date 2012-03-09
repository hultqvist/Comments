<?php
	//Comments
	echo '<h1>Your comments</h1>';
	$result = @mysql_query('
		SELECT Comments.*, Sites.SiteUrl FROM Comments
		JOIN Sites on Comments.SiteID=Sites.SiteID
		WHERE CommentEmail=\''.mysql_real_escape_string($session['Email']).'\'
	')
	 or die(mysql_error());

	require_once('../markdown.php');

	echo '<div id="comments">';
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		PrintComment(null, $row, $session);
	}
	echo '</ul>';
	echo '</div>';
