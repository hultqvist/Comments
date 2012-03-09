<?php
// Main script called by the pages using this service.
if(!isset($sid)) die('Missing sid');
header('Content-Type: text/javascript');

require_once('config.php');

//js version of transfering path into page
//See static.php for php version
?>
var page = window.location.hostname+" "+window.location.pathname+" "+window.location.search;
page = page.replace(/\//g, ' ');
page = page.replace(/  /g, ' ');
page = page.trim();
page = encodeURIComponent(page);

//For statusmessages to be displayed after the comments are reloaded.
var status;

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
			setTimeout(function(){req.send();},5000); //Retry every 5 seconds
			return;
		}
		ce.innerHTML = req.responseText;
		if(status)
			document.getElementById("commentStatus").innerHTML = status;
		status = null;
	};
	req.open('GET', '<?php echo service_url; ?>/inc/<?php echo $sid;?>/'+page+'.html?ajax&ref='+encodeURIComponent(document.referrer), true);
	req.send();
}

function commentPost()
{
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if(req.readyState != 4)
			return;
		if(req.status == 200) {
			status = req.responseText;
			loadComments();
		}
		else
			document.getElementById("commentStatus").innerHTML = "Error: " + req.status + ": " + req.statusText;
	};
	req.open('POST', '<?php echo service_url; ?>/post.php?sid=<?php echo $sid; ?>&page='+page);
	var parameters = 'commentText='+encodeURI(document.getElementById('commentText').value)+
		'&commentEmail='+encodeURI(document.getElementById('commentEmail').value)+
		'&ajax=true';
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//req.setRequestHeader("Content-Length", parameters.length);
	req.send(parameters);
	document.getElementById("commentStatus").innerHTML = "<p>Sending comment...<p>";
	return false;
}
