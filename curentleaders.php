<?php 
include_once 'config.php';

//Берем имя таблицы
$result = $pdo->query('SELECT * FROM leagueinfo');
$leagueinfo = $result->fetch();

$tname = $leagueinfo['table_name'];

$regtime = $leagueinfo['regtime'];
$starttime = $leagueinfo['starttime'];
$endtime = $leagueinfo['endtime'];

//Берем инфу по игроку из БД
$query = "SELECT 
    CS.points, 
    CS.battles, 
    CS.damage_dealt, 
    CS.avg_damage_assisted,
    CS.frags, 
    CS.winrate,
    CS.league,
    P.nickname
    FROM $tname AS CS
    JOIN leagueplayers AS P
    ON P.account_id = CS.id
    ORDER BY CS.points DESC
";

//Далее идут проверки на вывод информаци в соответствии с временем лиги
?>
<main>
<?php include_once 'header.php' ?>
<div>
<h1 class="league"><?=$leagueinfo['title_name']?></h1>
    <form class="list_of_leagues" method="get" action="/curentleaders">
        <button class="league_name" name="league" value="free">Free</button>
        <button class="league_name" name="league" value="normal">Normal</button>
    </form>
</div>
<div class="sbox">
    <?php if ( strtotime($starttime) - time() > 0): ?>
        <div class="leaguetable"><h1>Новая лига еще не началась!</h1></div>
    <?php elseif ( strtotime($endtime) - time() < 0 ): ?>
        <div class="leaguetable"><h1>Лига закончилась!</h1></div>
        <?php if (!empty($_GET['league']) && ($_GET['league'] == "free" || $_GET['league'] == "normal")) : ?>
        <div class="leaguetable">
            <h2>Подлига: <?=$_GET['league']?></h2>
            <div class="tablehead">
                <div class="cell player_position">№</div>
                <div class="cell player_name">Ник</div>
                <div class="cell player_points">Очки</div>
                <div class="cell player_damage">Урон</div>
                <div class="cell player_assist">Ассист</div>
                <div class="cell player_kills">Убийства</div>
                <div class="cell player_wins">% побед</div>
                <div class="cell player_battles">Бои</div>
            </div>
        </div>
            <?php $result = $pdo->query($query); $i = 0;
            while ($row = $result->fetch()): 
                if ($row['league'] == $_GET['league'] && $row['battles'] >= 100): $i++;?>
                    <div class="tablebody">
                        <div class="cell player_position"><?=$i?></div>
                        <div class="cell player_name"><?=$row['nickname']?></div>
                        <div class="cell player_points"><?=$row['points']?></div>
                        <div class="cell player_damage"><?=round($row['damage_dealt'])?></div>
                        <div class="cell player_assist"><?=round($row['avg_damage_assisted'])?></div>
                        <div class="cell player_kills"><?=$row['frags']?></div>
                        <div class="cell player_wins"><?=$row['winrate'] * 100 ?></div>
                        <div class="cell player_battles"><?=$row['battles']?></div>                
                    </div>
        <?php endif; endwhile; endif;?>
    <?php else: ?>
        <?php if (!empty($_GET['league']) && ($_GET['league'] == "free" || $_GET['league'] == "normal")) : ?>
        <div class="leaguetable">
            <h2>Подлига: <?=$_GET['league']?></h2>
            <div class="tablehead">
                <div class="cell player_position">№</div>
                <div class="cell player_name">Ник</div>
                <div class="cell player_points">Очки</div>
                <div class="cell player_damage">Урон</div>
                <div class="cell player_assist">Ассист</div>
                <div class="cell player_kills">Убийства</div>
                <div class="cell player_wins">% побед</div>
                <div class="cell player_battles">Бои</div>
            </div>
        </div>
            <?php $result = $pdo->query($query); $i = 0;
            while ($row = $result->fetch()): 
                if ($row['league'] == $_GET['league']): $i++;?>
                    <div class="tablebody">
                        <div class="cell player_position"><?=$i?></div>
                        <div class="cell player_name"><?=$row['nickname']?></div>
                        <div class="cell player_points"><?=$row['points']?></div>
                        <div class="cell player_damage"><?=round($row['damage_dealt'])?></div>
                        <div class="cell player_assist"><?=round($row['avg_damage_assisted'])?></div>
                        <div class="cell player_kills"><?=$row['frags']?></div>
                        <div class="cell player_wins"><?=$row['winrate'] * 100 ?></div>
                        <div class="cell player_battles"><?=$row['battles']?></div>                
                    </div>
        <?php endif; endwhile; endif; endif; ?>
</div>
</main>
	<?php include_once 'footer.php' ?>	
</body>
</html>