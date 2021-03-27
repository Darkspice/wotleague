<?php 

include_once 'config.php';



//-------------------для отладки----------------------------
/*echo '<pre>';
print_r($timers);
echo '</pre>';*/

// var_dump($row);

/* $start = microtime(true);
$time1 = microtime(true) - $start;
echo $time1; */
	
//----------------------------------------------------------

?>
<main>
<?php include_once 'header.php' ?>
	<div class="main_content">
		<a style="background-image: url(/images/main/info.jpg);" class="box_content" href="/faq">Информация</a>
		<a style="background-image: url(/images/main/participate.jpg);" class="box_content" href="/ticket">Принять участие</a>	
	</div>
	<div class="main_content">
		<a style="background-image: url(/images/main/leaders.jpg);" class="box_content" href="/curentleaders">Лидеры</a>
		<a style="background-image: url(/images/main/support.jpg);" class="box_content" href="/donation">Поддержать проект</a>		
	</div>
</main>
	<?php include_once 'footer.php' ?>	 
</body>
</html>
