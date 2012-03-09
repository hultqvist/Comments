<?php
//This is called when a person clicks the verification link in an email.

require_once("shared.php");

$email = isset($_GET['email'])? $_GET['email'] : null;
$code  = isset($_GET['code'])? $_GET['code'] : null;

if(isset($_GET['logout'])){

	$email = isset($_COOKIE['email'])? $_COOKIE['email'] : null;
	$session = isset($_COOKIE['session'])? $_COOKIE['session'] : null;
	$res = @mysql_query('UPDATE Authors
		SET Session=null
		WHERE Email=\''.mysql_real_escape_string($email).'\'
		AND Session=\''.mysql_real_escape_string($session).'\'')
		or die('<div class="commentError">'.mysql_error().'</div>');

	$url = parse_url(service_url);
	setcookie("session", "", time()-3600*365, $url['path'], $url['host'], $url['scheme'] === "https", true);
	setcookie("email", "", time()-3600*365, $url['path'], $url['host'], $url['scheme'] === "https", true);

	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}

//Check code
$res = @mysql_query('SELECT * FROM Authors WHERE Email=\''.mysql_real_escape_String($email).'\'')
	or die('<div class="commentError">'.mysql_error().'</div>');

$row = mysql_fetch_assoc($res);
if($row)
{
	if($code !== null && $row['VerifyCode'] === $code)
	{
		//We have a valid code, set session
		$session = sha1($email.$code.rand().time());
		$res = @mysql_query('UPDATE Authors
			SET VerifyCode=NULL, Session=\''.mysql_real_escape_String($session).'\'
			WHERE Email=\''.mysql_real_escape_String($email).'\'
			AND VerifyCode=\''.mysql_real_escape_String($code).'\'')
			or die('<div class="commentError">'.mysql_error().'</div>');
		if(!$res)
			die('<div class="commentError">Failed to update session</div>');

		$url = parse_url(service_url);
		setcookie("session", $session, time()+3600*365, $url['path'], $url['host'], $url['scheme'] === "https", true);
		//Allow javascript to access this cookie
		setcookie("email", $email, time()+3600*365, $url['path'], $url['host'], $url['scheme'] === "https", false);
		header('Location: '.service_url.'/dashboard/');
		return;
	}
}
?>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Comment Dashboard</title>
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<article>
<?php
$generateCode = FALSE;
if($row){
	//Limit one verification email per day, unless already verified
	if($row['VerifyCode'] === NULL)
	{
		$generateCode = TRUE;
	}
	elseif($row['VerifyCode'] !== $code)
	{
		if($code)
			echo '<div class="commentError">Invalid code.</div>';

		$vd = strtotime($row['VerifyDate']);
		if($vd > time() + 3600 * 2)
			$generateCode = TRUE;
		else
		{
			echo '<div class="commentStatus">Wait 2 hours and try again, or find the latest email.<br/>You can still continue to post comments.</div>';
		}
	}
}
else
{
	$generateCode = TRUE;
}
if($generateCode)
{
	GenerateAndSendVerificationCode($email, service_url);
	echo '<div class="commentStatus">New code sent, check your email.</div>';
}
?>
</article>
</body>
</html>
