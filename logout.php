<?php

include_once 'config.php';

//если игрок залогинен то обнуляем ему сессию
if ( $_SESSION['status'] != "ok" ) {
	header('Location '.AUTHURL);
} else {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.worldoftanks.ru/wot/auth/logout/");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"application_id=".APPLICATION_ID."&access_token=".$_SESSION['access_token']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$_SESSION = array();
	session_destroy();

	header ('Location: '.MAINURL);
}
?>