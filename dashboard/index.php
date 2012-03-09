<?php
header('Content-Type: text/html');

require_once("../shared.php");

$session = GetSessionConstants();
?>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment Dashboard</title>
	<link rel="stylesheet" href="../style.css" type="text/css" />
	<link rel="stylesheet" href="../comments.css" type="text/css" />
</head>
<body>
<header>
	<a href="?comments">Comments</a>
	<a href="?sites">Sites</a>
	<a href="?register">Register website</a>
	<strong><?php echo htmlentities($session['Email']);?></strong>
	<a href="<?php echo service_url;?>/logout/">Logout</a>
</header>
<article>
<?php
if($session)
{
	if(isset($_REQUEST['register']))
		require('register.php');
	elseif(isset($_GET['verify']))
		require('verify.php');
	elseif(isset($_GET['delete']))
		require('delete.php');
	elseif(isset($_GET['sid']))
		require('site.php');
	elseif(isset($_GET['sites']))
		require('sites.php');
	else
		require('comments.php');

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
