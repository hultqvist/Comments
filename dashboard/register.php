<?php

if(isset($_POST['register']) === FALSE)
{
	//Register site
?>
	<h1>Register new site</h1>
	<form action="?" method="post">
	<div>
		Url of your website:
		<input type="text" name="register" value="http://" />
		<input type="submit" value="Register new site" />
	</form>
<?php
	return;
}
if(sessionEmail == null)
	return;

$url = rtrim($_POST['register'], "/");
if(filter_var($url, FILTER_VALIDATE_URL) === FALSE)
{
	echo '<div class="commentError">Invalid url: '.htmlentities($url).'</div>';
	return;
}

//Check for existing websites
$res = mysql_query('SELECT * FROM Sites WHERE LOCATE(SiteUrl, \''.mysql_real_escape_string($url).'\') = 1');
if(!$res)
{
	echo('<div class="commentError">'.mysql_error().'</div>');
	return;
}
$count = mysql_num_rows($res);
if($count > 0)
{
	while ($row = mysql_fetch_assoc($res))
		echo('<div class="commentError">Already registered: '.htmlentities($row['SiteUrl']).'</div>');
	return;
}

//Register the site
$res = @mysql_query('INSERT INTO Sites (SiteUrl, AdminEmail)
	VALUES (\''.mysql_real_escape_string($url).'\', \''.mysql_real_escape_string(sessionEmail).'\')');
if(!$res)
{
	echo('<div class="commentError">'.mysql_error().'</div>');
	return;
}
$id = mysql_insert_id();

echo '<h1>Site registered</h1>';
echo '<p><a href="'.service_url.'/dashboard/?sid='.$id.'">Continue</a>';
