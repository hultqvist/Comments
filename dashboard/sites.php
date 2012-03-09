<?php
	if(!$session)
		die('no session');
		
	//Sites
	if($session['Email'] == service_email){
		echo '<h1>All sites</h1>';
		$result = @mysql_query('SELECT * FROM Sites');
	} else {
		echo '<h1>Your sites</h1>';
		$result = @mysql_query('SELECT * FROM Sites WHERE AdminEmail=\''.mysql_real_escape_string($session['Email']).'\'');
	}

	if(!$result) die(mysql_error());
	
	echo '<ul>';
	while ($row = mysql_fetch_assoc($result)) {
		echo '<li>
		<a href="?sid='.$row['SiteID'].'">'.htmlentities($row['SiteUrl']).'</a>
		</li>';
	}
	echo '</ul>';

