<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="/css/styles.css">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<meta name="yandex-verification" content="3bae79d6d59db68c" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Сайт предназначен для проведения между игроками соревновательных лиг. Соревнования проводятся в стандартном бою и любой желающий сможет принять участие. По окончанию лиги подводится итог и награждаются лучшие игроки.">
		<meta name="keywaords" content="league, world of tanks">
		<title>Соревновательная платформа для игры World of Tanks</title>
		<link href="https://fonts.googleapis.com/css?family=Bebas+Neue|Oswald|Montserrat|Russo+One|Roboto+Condensed&display=swap" rel="stylesheet"> 
		<!-- Yandex.Metrika counter -->
		<script type="text/javascript" >
		(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
		m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
		(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

		ym(61534741, "init", {
				clickmap:true,
				trackLinks:true,
				accurateTrackBounce:true
		});
		</script>
		<noscript><div><img src="https://mc.yandex.ru/watch/61534741" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
	</head>
	<body>
 	<header class="login">
 		<?php if (!isset($_SESSION['status'])): ?>
 			<a href=<?=AUTHURL?> class="login">Вход</a>
		 <?php else: ?>
			<span class="nick_name"><?=$_SESSION['nickname']?></span>
			<a href="/personalpage" class="login">Личный кабинет</a><!--
		 --><a href=<?=LOGOUTURL?> class="login">Выход</a><!--
		 <?php endif;?>
		 <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == true) : ?>
		 	--><a href=<?=ADMINURL?> class="login">Админ панель</a> 			
 		<?php endif;?>
	 <div class="name">
 		<a href=<?=MAINURL?> class="logo">World of Tanks <span style="color: #ff7433">League</span></a>
 	</div>
	</div>
	</header>