<?php

$result = $pdo->query('SELECT table_name FROM leagueinfo');
$tname = $result->fetchColumn();

$regplayers = '';
//если лига закончилась
if ( strtotime($endtime) - time() < 0 ) {

	$result = $pdo->query('SELECT id FROM leaguestart');
	while ($row = $result->fetchColumn()) {
		$regplayers .= (string)$row . ',';
	}
		
	//отправляем запрос на сборку данных по игрокам из API WG
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.worldoftanks.ru/wot/account/info/");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"application_id=".APPLICATION_ID."&account_id=$regplayers&extra=statistics.random&fields=-statistics.all%2C-statistics.clan%2C-statistics.company%2C-statistics.historical%2C-statistics.regular_team%2C-statistics.stronghold_defense%2C-statistics.stronghold_skirmish%2C-statistics.team");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$pdata=json_decode(curl_exec($ch),true);
	curl_close ($ch);
	
	//заносим в массив данные из API WG
	if ($pdata['status'] == 'ok') {
        $leaguestart_data = [];
        $result = $pdo->query('SELECT * FROM leaguestart');
        while ($row = $result->fetch()) {
            $leaguestart_data[$row['id']] = [
                'avg_damage_assisted' => $row['avg_damage_assisted'],
                'battles' => $row['battles'],
                'damage_dealt' => $row['damage_dealt'],
                'dropped_capture_points' => $row['dropped_capture_points'],
                'frags' => $row['frags'],                
                'spotted' => $row['spotted'],
                'wins' => $row['wins'],
            ];
		}
			
		//Ключ - айди игрока, значение - массив с данными
		$pinfo = $pdata['data'];
		
		foreach ($pinfo as $key => $value) {			
			
			//смотрим не закрыта ли лига у игрока
			$query = 'SELECT close FROM leagueplayers WHERE account_id = ?';
            $result = $pdo->prepare($query);
			$params = [$key];
			$result->execute($params);
			$close = $result->fetchColumn();

			//если лига открыта то выполняем
            if( $close != 1) {
                $battles = $value['statistics']['random']['battles'] - 
                    $leaguestart_data[$key]['battles'];
				
				//если боев больше чем 0 то записываем полученные данные из API WG в нашу БД
			    if ( $battles > 0) {
				    $avg_damage_assisted = round(
					    (($value['statistics']['random']['battles'] * $value['statistics']['random']        ['avg_damage_assisted']) - ($leaguestart_data[$key]['battles'] * $leaguestart_data[$key]['avg_damage_assisted']))/$battles, 2);
	
				    $damage_dealt = round(
					    ($value['statistics']['random']['damage_dealt'] - 
					    $leaguestart_data[$key]['damage_dealt'])/$battles, 2);
	
				    $dropped_capture_points = round(
					    ($value['statistics']['random']['dropped_capture_points'] - 
					    $leaguestart_data[$key]['dropped_capture_points'])/$battles, 2);
	
				    $frags = round(
					    ($value['statistics']['random']['frags'] - 
					    $leaguestart_data[$key]['frags'])/$battles, 2);
	
				    $spotted = round(
					    ($value['statistics']['random']['spotted'] - 
					    $leaguestart_data[$key]['spotted'])/$battles, 2);
	
				    $winrate = round(
					    ($value['statistics']['random']['wins'] - 
					    $leaguestart_data[$key]['wins'])/$battles, 2);
	
				    $points = round(
				    ($avg_damage_assisted * 0.5) + ($damage_dealt * 1) + ($dropped_capture_points * 50) + ($frags * 500) + ($spotted * 200) + ($winrate * 200));
				
					$query = "INSERT INTO $tname (id, league, points, avg_damage_assisted, battles, damage_dealt, dropped_capture_points, frags, spotted, winrate)
					VALUES (
					:id_1,
					(SELECT league FROM leagueplayers WHERE account_id = :id_2),
					:points,
					:avg_damage_assisted,
					:battles,
					:damage_dealt,
					:dropped_capture_points, 
					:frags, 
					:spotted,
					:winrate)
					ON DUPLICATE KEY UPDATE
					avg_damage_assisted = VALUES (avg_damage_assisted),
					battles = VALUES (battles),
					damage_dealt = VALUES (damage_dealt),
					dropped_capture_points = VALUES (dropped_capture_points),
					frags = VALUES (frags),
					spotted = VALUES (spotted),
					winrate = VALUES (winrate)
					";
				    $result = $pdo->prepare($query);
				    $params = [
					':id_1' => $key,
					':id_2' => $key,
				    ':points' => $points,
				    ':avg_damage_assisted' => $avg_damage_assisted,
				    ':battles' => $battles,
				    ':damage_dealt' => $damage_dealt,
				    ':dropped_capture_points' => $dropped_capture_points,
				    ':frags' => $frags,
				    ':spotted' => $spotted,
				    ':winrate' => $winrate
				    ];
					$result->execute($params);
					$testinfo = $result->fetch();	
					
					//Начисляем очки
					$query = 'UPDATE leagueplayers SET points = points + ? WHERE account_id = ?';
					$result = $pdo->prepare($query);
					$params = [$points, $key];
					$result->execute($params);
				}
                //Закрываем лигу для данного игрока после обновления его данных
                $query = 'UPDATE leagueplayers SET close = 1, league = DEFAULT WHERE account_id = ?';
                $result = $pdo->prepare($query);
			    $params = [$key];
				$result->execute($params);				
            }
	    }
    }
}
?>