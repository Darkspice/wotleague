<?php

include_once 'config.php';
//проверка на админа
if (!isset($_SESSION['isadmin']) || $_SESSION['isadmin'] !=true) {
	header ('Location: '.MAINURL);
}
//Старт новой лиги
if ( !empty($_POST['submit']) && !empty($_POST['regtime']) && !empty($_POST['starttime']) && !empty($_POST['endtime']) && !empty($_POST['table_name']) && !empty($_POST['title_name']) ) {

	//если нет таблицы, то создаем
	$result = $pdo->query('SHOW TABLES LIKE "leagueinfo"');
	if ( $result->rowCount() == 0) {
		$query = 'CREATE TABLE leagueinfo (
		id PRIMARY KEY,
		table_name VARCHAR(20),
		title_name VARCHAR(20),
		regtime TIMESTAMP,
		starttime TIMESTAMP,
		endtime TIMESTAMP 
		)';
		$pdo->query($query);
	}
	
	//вставляем в таблицу инфу новой лиги
	$query = 'INSERT INTO leagueinfo (id, table_name, title_name, regtime, starttime, endtime) 
	VALUES ("1", ?, ?, ?, ?, ? )
	ON DUPLICATE KEY UPDATE
	table_name = VALUES (table_name),
	title_name = VALUES (title_name),
	regtime = VALUES (regtime),
	starttime = VALUES (starttime),
	endtime = VALUES (endtime)
	';
	$result = $pdo->prepare($query);
	$params = [$_POST['table_name'], $_POST['title_name'], $_POST['regtime'], $_POST['starttime'], $_POST['endtime']];
	$result->execute($params);
	
	$result = $pdo->query('SELECT table_name FROM leagueinfo');
	$tname = $result->fetchColumn();
	
	//создаем таблицу новой лиги
	$query = "CREATE TABLE IF NOT EXISTS $tname (
	id INT PRIMARY KEY,
	league VARCHAR(20),
	points INT,
	avg_damage_assisted FLOAT,
	battles INT,
	damage_dealt FLOAT,
	dropped_capture_points FLOAT,
	frags FLOAT,
	spotted FLOAT,
	winrate FLOAT
	)";
	$pdo->query($query);
	
	//Сбрасываем параметр закрытой лиги у игроков.
	$result = $pdo->query('UPDATE leagueplayers SET close = 0');	

} else  {
	echo "Нужно заполнить все поля!";
}

//выбираем время
$result = $pdo->query('SELECT * FROM leagueinfo');
$timers = $result->fetch();

$regtime = $timers['regtime'];
$starttime = $timers['starttime'];
$endtime = $timers['endtime'];

//Заносим данные по игрокам в БД в новой лиги
if ( isset($_POST['getdata']) ) {
	if ( strtotime($regtime) - time() > 0 ) {
		echo "Регистрация всё еще идет!";
	} else if (strtotime($starttime) - time() < 0 && strtotime($endtime) - time() > 0) {
		echo "Лига уже началась!";
	} else {
		include_once 'startleague.php';
	}
}
if ( isset($_POST['updata']) ) {
	if ( strtotime($starttime) - time() > 0 ) {
		echo "Лига еще не началась!";
	} else if (strtotime($endtime) - time() < 0) {
		echo "Лига закончилась!";
	} else {
		include_once 'upleague.php';
	}
}
if ( isset($_POST['closedata']) ) {
	if ( strtotime($endtime) - time() > 0 ) {
		echo "Лига еще не закончилась!";
	} else {
		include_once 'closeleague.php';
	}
}

?>
<main>
<?php include_once 'header.php' ?>
 	<div class="settime">
 		<div class="timeevent">
 			Таймер лиг
 			<form action="admin.php" method="post">
 				<div class="timer"><input type="datetime" name="regtime"> Конец регестрации</input></div>
 				<div class="timer"><input type="datetime" name="starttime"> Начало лиги</input></div>
 				<div class="timer"><input type="datetime" name="endtime"> Конец лиги</input></div>
				<div class="timer"><input name="table_name"> Название в ДБ</input></div>
				<div class="timer"><input name="title_name"> Название лиги</input></div>
 				<div class="timer"><input type="submit" name="submit" value="Установить"></div>
 			</form>
 		</div> 					
 		</div> 		
	 </div>
	 <div class="stats">
	 	Управление данных лиг
		<form action="admin.php" method="post">
	 	<button name="getdata" value="ok">Начало лиги</button>
	 	<button name="updata" value="ok">Обновить данные</button>
	 	<button name="closedata" value="ok">Конец лиги</button>
		</form>
	 </div>
</main>
	<?php include_once 'footer.php' ?>	 
</body>
</html>