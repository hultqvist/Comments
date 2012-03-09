<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comments</title>
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
	<article>
<?php
	require('markdown.php');
	echo Markdown(file_get_contents('README.markdown'));
?>
	<h2>Love your comments</h2>
	<!-- Start of comment code -->
	<div id="comments"></div>
	<script type="text/javascript" src="http://silentorbit.com/comments/inc/1/script.js" async="async"></script>
	<noscript>
		<object data="http://silentorbit.com/comments/inc/1/ref.html" width="600" height="500" />
	</noscript>
	<!-- End of comment code -->
	</article>
</body>
</html>
