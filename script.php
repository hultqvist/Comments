<?php
header('Content-Type: text/javascript');
require_once('parameters.php');
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
	req.open('POST', '<?php echo $service_url; ?>/post.php?sid=<?php echo $siteID; ?>&url=<?php echo urlencode($siteUrl); ?>', true);
	var parameters = 'commentText='+encodeURI(document.getElementById('commentText').value)+
		'&commentEmail='+encodeURI(document.getElementById('commentEmail').value);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Content-Length", parameters.length);
	req.send(parameters);
	document.getElementById("commentStatus").innerHTML = "<p>Sending comment...<p>";
	return false;
}
