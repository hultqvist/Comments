<?php
// Main script called by the pages using this service.

header('Content-Type: text/javascript');

require_once('../config.php');

//Load $siteID and $pageUrl
require_once('../parameters.php');

?>
loadComments();

function loadComments()
{
	var ce = document.getElementById("comments");
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if(req.readyState != 4)
			return;
		if(req.status == 500){
			ce.innerHTML = "Error: " + req.status + ": " + req.statusText;
			setTimeout(function(){req.send();},5000);
			return;
		}
		ce.innerHTML = req.responseText;
	};
	req.open('GET', '<?php echo service_url; ?>/comments/?form=1&sid=<?php echo $siteID; ?>&url=<?php echo urlencode($pageUrl); ?>', true);
	req.send();
}

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
	req.open('POST', '<?php echo service_url; ?>/post/?sid=<?php echo $siteID; ?>&url=<?php echo urlencode($pageUrl); ?>', true);
	var parameters = 'commentText='+encodeURI(document.getElementById('commentText').value)+
		'&commentEmail='+encodeURI(document.getElementById('commentEmail').value);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Content-Length", parameters.length);
	req.send(parameters);
	document.getElementById("commentStatus").innerHTML = "<p>Sending comment...<p>";
	return false;
}
