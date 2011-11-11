<?php

	require_once('../shared.php');

	$url = parse_url(service_url);
	setcookie("session", "", time()-3600*365, $url['path'], $url['host'], $url['scheme'] === "https", true);
	setcookie("email", "", time()-3600*365, $url['path'], $url['host'], $url['scheme'] === "https", true);

	header('Location: '.$_SERVER['HTTP_REFERER']);

