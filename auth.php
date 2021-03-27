<?php

include_once 'config.php';

if(empty($_GET['status'])){//генерируем ссылку и перенаправяем пользователя
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.worldoftanks.ru/wot/auth/login/");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"application_id=".APPLICATION_ID."&expires_at=300&redirect_uri=".AUTHURL."&nofollow=1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data=json_decode(curl_exec($ch),true);
	curl_close ($ch);

	if($data['status']=='ok'){
		header ('Location: '.$data['data']['location']);
		exit();
	}else{
		exit('Не удалось получить ссылку для перенаправления.');
	}
}elseif(isset($_GET['status']) && isset($_GET['access_token']) && isset($_GET['nickname']) && isset($_GET['account_id']) && isset($_GET['expires_at'])){//если пользователь попал на страницу с параметрами, которые устанавливает метод auth/login
	if($_GET['status']!="ok"){
		$error_code=500;
		if(preg_match('/^[0-9]+$/u', $_GET['code'])){
			$error_code=$_GET['code'];
		}
		exit("Ошибка авторизации. Код ошибки: $error_code");
	}elseif($_GET['expires_at']<time()){
		exit("Ошибка авторизации. Срок действия access_token истек.");
	}else{
		$context = stream_context_create(
			array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query(
						array(
							'expires_at' => 14*24*60*60,
							'access_token' => $_GET['access_token'],
							'application_id' => APPLICATION_ID
						)
					)
				)
			)
		);
		$data=json_decode(@file_get_contents('https://api.worldoftanks.ru/wot/auth/prolongate/', false, $context),true);//подтверждаем правдивость полученных параметров
		if($data['status']=="ok"){
			$access_token=$data['data']['access_token'];
			$expires_at=$data['data']['expires_at'];
			$account_id=$data['data']['account_id'];
			//здесь вам нужно установить пользователю куки, записать его токен в БД, сделать все то, что сочтете нужным.
			//создаем сессию по данным игрока
			if ( !isset($_SESSION['status']))  {
				$_SESSION['status'] = $_GET['status'];
				$_SESSION['access_token'] = $_GET['access_token'];
				$_SESSION['nickname'] = $_GET['nickname'];
				$_SESSION['account_id'] = $_GET['account_id'];
				$_SESSION['expires_at'] = $_GET['expires_at'];

				//вносим инфу в БД игроков
				$result = $pdo->query('SHOW TABLES LIKE "leagueplayers"');
				if ( $result->rowCount() == 0 ) {
					$query = 'CREATE TABLE leagueplayers (
					account_id INT PRIMARY KEY,
					nickname VARCHAR(20),
					fake INT DEFAULT 0,
					tickets INT DEFAULT 0,
					league varchar(20) DEFAULT NULL,
					close BOOLEAN DEFAULT 0,
					points INT DEFAULT 0,
					lastentrance TIMESTAMP
					)';
					$pdo->query($query);				
				}				
				
				$query = 'INSERT INTO leagueplayers (account_id, nickname, lastentrance)
				VALUES ( ?, ?, CURRENT_TIMESTAMP() )
				ON DUPLICATE KEY UPDATE
				nickname = VALUES (nickname),
				fake = DEFAULT,
				lastentrance = VALUES (lastentrance)
				';
				$result = $pdo->prepare($query);
				$params = [$_SESSION['account_id'], $_SESSION['nickname']];
				$result->execute($params);
			}
			//присваиваем админа
			if ($_SESSION['nickname'] == "biba333" && $_SESSION['account_id'] == 333) {
				$_SESSION['isadmin'] = true;
			}
			header ('Location: '.MAINURL);
			exit('Это пользователь с id <b>'.$account_id.'</b><br />Токен <b>'.$access_token.'</b>, он подтвержден и действует до <b>'.date("d.m.Y H:i:s",$expires_at).'</b>');
		}else{
			exit('access_token не подтвержден');
		}
	}
}else{
	$error_code=500;
	if(preg_match('/^[0-9]+$/u', $_GET['code'])){
		$error_code=$_GET['code'];
	}
	exit("Произошла ошибка. Код ошибки: $error_code");
}
?>
<!DOCTYPE HTML>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 </head>
</html>