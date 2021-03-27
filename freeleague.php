<?php

include_once 'config.php';

//если игрок залогинен
if( !isset($_SESSION['status']) ) {
	header ('Location: '.AUTHURL); 
	exit();
}

//нажали кнопку фри лиги
if ( isset($_POST['freebutton']) && $_SESSION['status'] == "ok") {
	
	$l_message = '';

	//выборка времени
	$result = $pdo->query('SELECT * FROM leagueinfo');
	$timers = $result->fetch();

	//регистрируем игрока в лиги если идет регистрация...
	if ( (strtotime($timers['regtime']) - time()) > 0) {
	
		$query = 'SELECT * FROM leagueplayers WHERE account_id = ?';

		$result = $pdo->prepare($query);
		$params = [$_SESSION['account_id']];
		$result->execute($params);

		$pinfo = $result->fetch();
		
		// ... и он не зареган ни в какой из лиг
		if( $pinfo['league'] == NULL ) {

			$query = 'UPDATE leagueplayers SET league = "free" WHERE account_id = ?';

			$result = $pdo->prepare($query);
			$params = [$_SESSION['account_id']];
			$result->execute($params);

			$l_message = "Вы успешно зарегистрировались во Free лиги";
		}
		else {
			if ( $pinfo['league'] == 'free') {
				$l_message = "Вы уже зарегистрированы во Free лиги";
			}
			else if ( $pinfo[0]['league'] == 'normal' ) {
				$l_message = "Вы уже зарегистрированы в Normal лиги";
			}	
		}
	}
}
else {
	header ('Location: '.MAINURL);
}
?>

<main>
<?php include_once 'header.php' ?>
 	<div>
 		<div class="reginfo">
		<?php if ( (strtotime($timers['regtime']) - time()) < 0): ?>
		 	<div class = "mess">Регистрация закончилась</div>
		<?php elseif ( $pinfo['league'] != 'free' ): ?>
			<div class="nick"> <?= $_SESSION['nickname']?></div>
			<div class="mess"> <?= $l_message ?></div>			
		<?php else: ?>
			<div class="nick"> <?= $_SESSION['nickname']?></div>
			<div class="mess"> <?= $l_message ?></div>
			<form method="post" action="<?=UNDO?>">
			<button class="undobutton" name="undo" value="ok">Отменить регистрацию</button>
			</form>
		<?php endif; ?>
		</div>
	 </div>
</main>
	<?php include_once 'footer.php' ?>	
</body>
</html>