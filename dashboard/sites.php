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

