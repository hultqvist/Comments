<?php
// Main script called by the pages using this service.

//Close connection since it is only called once for every page load.
header('Connection: close');
//Allow cross site posting, enable other sites to use your service
//Remove these two header lines if you only use the service from the same site.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

//Load $siteID and $pageUrl
require_once('parameters.php');

if(isset($_POST['commentText']))
{
	header('Content-Type: text/html');
	require('post.php');
	return;
}

header('Content-Type: text/javascript');

?>
document.getElementById("comments").innerHTML = '<?php

ob_start();
	require('comments.php');
	$comments = ob_get_contents();
ob_end_clean();
	$comments = str_replace(array("\r", "\n"), "\\n", $comments);
	echo $comments;
?>';

function commentPost()
{
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if(req.readyState != 4)
			return;
		if(req.status == 200)
			document.getElementById("commentStatus").innerHTML = req.responseText;
		else
			document.getElementById("commentStatus").innerHTML = "Error: " + req.status + ": " + req.statusText;
	};
	req.open('POST', '<?php echo $service_url; ?>/script.php?sid=<?php echo $siteID; ?>&url=<?php echo urlencode($pageUrl); ?>', true);
	var parameters = 'commentText='+encodeURI(document.getElementById('commentText').value)+
		'&commentEmail='+encodeURI(document.getElementById('commentEmail').value);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Content-Length", parameters.length);
	req.send(parameters);
	document.getElementById("commentStatus").innerHTML = "<p>Sending comment...<p>";
	return false;
}
