<?php
	if(!sessionEmail)
		return;

	GetSiteConstants(FALSE);

	if(urlError)
		echo urlError;

	//Sites
	$result = @mysql_query('SELECT * FROM Sites WHERE AdminEmail=\''.mysql_real_escape_string(sessionEmail).'\' AND SiteID='.siteID)
	 or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	if(!$row) {
		echo 'No site with sid='.siteID;
		return;
	}

	$siteUrl = htmlentities($row['SiteUrl']);
	echo '<h1>'.$siteUrl.'</h1>';
	echo '<a href="'.$siteUrl.'">'.$siteUrl.'</a>';

	echo '<h1>HTML code</h1>';
	echo '<p>Put the following two lines on every page you want comments</p>';
	echo '<code>';
	echo htmlentities('<div id="comments">Loading comments...</div>
<script type="text/javascript" src="'.service_url.'/script/?sid='.siteID.'" async="async"></script>');
	echo '</code>';


	//Comments
	echo '<h1>Comments</h1>';
	$result = @mysql_query('
		SELECT * FROM Comments
		WHERE SiteID='.siteID)
	or die(mysql_error());

	require_once('../markdown.php');

	echo '<div id="comments">';
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		PrintComment($row);
	}
	echo '</ul>';
	echo '</div>';

