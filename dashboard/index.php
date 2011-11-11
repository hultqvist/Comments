<?php
header('Content-Type: text/html');

require_once("../shared.php");

GetSessionConstants();
?>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment Dashboard</title>
	<link rel="stylesheet" href="../style.css" type="text/css" />
</head>
<body>
<article>
<div>
	<a href="?">Start</a>
</div>
<?php
if(sessionEmail !== null)
{
	echo '<h1>'.htmlentities(sessionEmail).' <a href="'.service_url.'/logout/">logout</a></h1>';

	if(isset($_GET['verify']))
		require('verify.php');
	elseif(isset($_GET['delete']))
		require('delete.php');
	elseif(isset($_GET['sid']))
		require('site.php');
	else
		require('main.php');

mysql_close();

}
else
{
?>
	<h1>Not logged in</h1>
	<form action="<?php echo service_url;?>/authorize" method="GET">
	<div>
		E-mail:
		<input type="text" name="email" />
		<input type="submit" value="Send me authorization link" />
	</div>
	</form>
<?php
}
?>
</article>
</body>
</html>
