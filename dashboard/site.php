<?php
	if(!$session)
		return;

	$sid = intval($_GET['sid']);

	//Sites
	if($session['Email'] == service_email){
		$result = @mysql_query('SELECT * FROM Sites WHERE SiteID='.$sid);
	} else {
		$result = @mysql_query('SELECT * FROM Sites WHERE AdminEmail=\''.mysql_real_escape_string($session['Email']).'\' AND SiteID='.$sid);
	}
	if(!$result) die(mysql_error());
	
	$site = mysql_fetch_assoc($result) or die('No site with sid='.$sid);

	$siteUrl = htmlentities($site['SiteUrl']);
	echo '<h1>'.$siteUrl.'</h1>';
	echo '<a href="'.$siteUrl.'">'.$siteUrl.'</a>';

	echo '<h1>HTML code</h1>';
	echo '<p>Put the following code on every page you want comments</p>';
	echo '<code>';
	echo htmlentities('<div id="comments"></div>
<script type="text/javascript" src="'.service_url.'/inc/'.$sid.'/script.js" async="async"></script>
<noscript><object data="'.service_url.'/inc/'.$sid.'/ref.html" width="600" height="500" /></noscript>');
	echo '</code>';

	//Comments
	echo '<h1>Comments</h1>';
	$result = @mysql_query('
		SELECT * FROM Comments
		WHERE SiteID='.$sid)
	or die(mysql_error());

	require_once('../markdown.php');

	echo '<div id="comments">';
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		PrintComment($site, $row, $session);
	}
	echo '</ul>';
	echo '</div>';


	//Links
	echo '<h1>Links</h1>';
	$result = @mysql_query('
		SELECT Page,Referer,COUNT(DISTINCT VisitorIP) as Count FROM Links
		WHERE SiteID='.$sid.'
		GROUP BY Referer
		ORDER BY Page')
	or die(mysql_error());

	echo '<div id="comments">';
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		PrintLink($row);
	}
	echo '</ul>';
	echo '</div>';

