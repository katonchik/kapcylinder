<h3 class="page_title">Дух игры: <a href="<?php echo SITE_URL; ?>/spirit/">Сводная статистика</a> / <a href="<?php echo SITE_URL; ?>/spirit_standard/">Стандарт</a> / <a href="<?php echo SITE_URL; ?>/spirit_easy/">Easy level</a> / Центрированный</h3>

<div id="mainblock_inside">

<?php
$s = array( 'total DESC', 'date', 'appraiser', 's1.game_id', 'receiver', 'rules', 'fouls', 'fair', 'attitude', 'compared', 'total', 'mvp', 'msp', 'mpp');

if(isset($_GET['key']))
{
	$key = $_GET['key'];
	if (!preg_match("|^[\D]+$|", $key))
	{
		exit("Error!");
	}
	$key = str_replace ("_desc", " DESC", $key);
}
//else $key = "s2.game_id";
else
{
	$key = "total DESC";
}

if (isset($key) && (in_array($key, $s) || in_array(str_replace (" DESC", "", $key), $s)))
{
	/*$result = $mysqli->query("
	SELECT time as date, t1.name as appraiser, t2.name as receiver, s2.rules, s2.fouls, s2.fair, s2.attitude, s2.compared, s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared as total,
		concat(p1.firstname, '<br>', p1.lastname) AS  mvp, concat(p2.firstname, '<br>', p2.lastname) AS  msp, concat(p3.firstname, '<br>', p3.lastname) AS  mpp
	FROM `uo_spirit_my` as s1, `uo_spirit_my` as s2, `uo_team` as t1, `uo_team` as t2, `uo_game`, `uo_player` as p1, `uo_player` as p2, `uo_player` as p3
	WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s1.receiver = t1.team_id AND s2.receiver = t2.team_id AND s1.game_id=uo_game.game_id AND s2.mvp=p1.player_id AND s2.msp=p2.player_id AND s2.mpp=p3.player_id AND s2.receiver < 1017
	ORDER BY ".mysql_escape_string(htmlspecialchars($key)).", date", $link);*/
	
	$key = str_replace ("appraiser", "receiver", $key);
	$i = 2;
	$result = $mysqli->query("
		SELECT t2.name as receiver, SUM(s2.rules - sp_avg.rules2) AS rules, SUM(s2.fouls - sp_avg.fouls2) AS fouls, SUM(s2.fair - sp_avg.fair2) AS fair, SUM(s2.attitude - sp_avg.attitude2) as attitude, SUM(s2.compared - sp_avg.compared2) as compared, SUM(s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared-(sp_avg.rules2+sp_avg.fouls2+sp_avg.fair2+sp_avg.attitude2+sp_avg.compared2)) as total
		FROM `uo_spirit_my` as s2, `uo_team` as t2, `uo_spirit_my` as s1
		INNER JOIN ( SELECT ss1.receiver, AVG(ss2.rules) AS rules2, AVG(ss2.fouls) AS fouls2, AVG(ss2.fair) AS fair2, AVG(ss2.attitude) as attitude2, AVG(ss2.compared) as compared2
				FROM `uo_spirit_my` as ss1, `uo_spirit_my` as ss2
				WHERE ss1.receiver <> ss2.receiver AND  ss1.game_id = ss2.game_id AND ss2.receiver < 1017
				GROUP BY ss1.receiver) AS sp_avg
				ON s1.receiver = sp_avg.receiver
		WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s2.receiver = t2.team_id AND s2.receiver < 1017
		GROUP BY receiver
		ORDER BY ".$mysqli->real_escape_string($key).", receiver DESC");
	//", $link);
	//ORDER BY ".mysql_escape_string(htmlspecialchars($key)), $link);
        if(!$result) {echo "неудачный запрос";exit();}
}
else exit("неверный формат запроса!");
if ($result->num_rows > 0)
{
        $myrow = $result->fetch_array(MYSQL_ASSOC);
	print "<br><table width=100% class=spirit>
	<tr class=headers>
	<td width=24%>Получили <span class=arrows><a href=?key=receiver>↑</a> <a href=?key=receiver_desc>↓</a></span></td>
	<td width=11%>Правила <span class=arrows><a href=?key=rules>↑</a> <a href=?key=rules_desc>↓</a></span></td>
	<td width=10%>Фолы <span class=arrows><a href=?key=fouls>↑</a> <a href=?key=fouls_desc>↓</a></span></td>
	<td width=18%>Справедливость <span class=arrows><a href=?key=fair>↑</a> <a href=?key=fair_desc>↓</a></span></td>
	<td width=15%>Отношение <span class=arrows><a href=?key=attitude>↑</a> <a href=?key=attitude_desc>↓</a></span></td>
	<td width=13%>Сравнение <span class=arrows><a href=?key=compared>↑</a> <a href=?key=compared_desc>↓</a></span></td>
	<td width=15%>Итого <span class=arrows><a href=?key=total>↑</a> <a href=?key=total_desc>↓</a></span></td>
	</tr>";
   do 
    {
           printf ("<tr><td>%s</td><td>%.2f</td>
           <td>%.2f</td><td>%.2f</td><td>%.2f</td>
           <td>%.2f</td><td>%.2f</td>", 
		   
		   $myrow["receiver"], 
		   ($myrow["rules"] ? $myrow["rules"] : "-"), 
		   ($myrow["fouls"] ? $myrow["fouls"] : "-"), 
		   ($myrow["fair"] ? $myrow["fair"] : "-"), 
		   ($myrow["attitude"] ? $myrow["attitude"] : "-"), 
		   ($myrow["compared"] ? $myrow["compared"] : "-"), 
		   ($myrow["rules"] & $myrow["fouls"] & $myrow["fair"] & $myrow["attitude"] & $myrow["compared"] ? $myrow["rules"] + $myrow["fouls"] + $myrow["fair"] + $myrow["attitude"] + $myrow["compared"] : "-")	//TBD 
		   );
    }
    while ($myrow = $result->fetch_array(MYSQL_ASSOC));
    print "</table>";
}
else
{
        echo "таблица пуста";
        exit();
}






if (isset($key) && (in_array($key, $s) || in_array(str_replace (" DESC", "", $key), $s)))
{
	/*$result = $mysqli->query("
	SELECT time as date, t1.name as appraiser, t2.name as receiver, s2.rules, s2.fouls, s2.fair, s2.attitude, s2.compared, s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared as total,
		concat(p1.firstname, '<br>', p1.lastname) AS  mvp, concat(p2.firstname, '<br>', p2.lastname) AS  msp, concat(p3.firstname, '<br>', p3.lastname) AS  mpp
	FROM `uo_spirit_my` as s1, `uo_spirit_my` as s2, `uo_team` as t1, `uo_team` as t2, `uo_game`, `uo_player` as p1, `uo_player` as p2, `uo_player` as p3
	WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s1.receiver = t1.team_id AND s2.receiver = t2.team_id AND s1.game_id=uo_game.game_id AND s2.mvp=p1.player_id AND s2.msp=p2.player_id AND s2.mpp=p3.player_id AND s2.receiver < 1017
	ORDER BY ".mysql_escape_string(htmlspecialchars($key)).", date", $link);*/
	
	$i = 2;
	$result = $mysqli->query("
		SELECT t$i.name as receiver, SUM(s2.rules - sp_avg.rules) AS rules, SUM(s2.fouls - sp_avg.fouls) AS fouls, SUM(s2.fair - sp_avg.fair) AS fair, SUM(s2.attitude - sp_avg.attitude) as attitude, SUM(s2.compared - sp_avg.compared) as compared, SUM(s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared-(sp_avg.rules+sp_avg.fouls+sp_avg.fair+sp_avg.attitude+sp_avg.compared)) as total
		FROM `uo_spirit_my` as s2, `uo_team` as t2, `uo_spirit_my` as s1
		INNER JOIN ( SELECT ss1.receiver, AVG(ss2.rules) AS rules, AVG(ss2.fouls) AS fouls, AVG(ss2.fair) AS fair, AVG(ss2.attitude) as attitude, AVG(ss2.compared) as compared
				FROM `uo_spirit_my` as ss1, `uo_spirit_my` as ss2
				WHERE ss1.receiver <> ss2.receiver AND  ss1.game_id = ss2.game_id AND ss2.receiver > 1016
				GROUP BY ss1.receiver) AS sp_avg
				ON s1.receiver = sp_avg.receiver
		WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s2.receiver = t2.team_id AND s2.receiver > 1016
		GROUP BY receiver
		ORDER BY ".$mysqli->real_escape_string($key).", receiver DESC");
	//", $link);
	//ORDER BY ".mysql_escape_string(htmlspecialchars($key)), $link);
        if(!$result) {echo "неудачный запрос";exit();}
}
else exit("неверный формат запроса!");
if ($result->num_rows > 0)
{
        $myrow = $result->fetch_array(MYSQL_ASSOC);
	print "<br><table width=100% class=spirit>
	<tr class=headers>
	<td width=24%>Получили <span class=arrows><a href=?key=receiver>↑</a> <a href=?key=receiver_desc>↓</a></span></td>
	<td width=11%>Правила <span class=arrows><a href=?key=rules>↑</a> <a href=?key=rules_desc>↓</a></span></td>
	<td width=10%>Фолы <span class=arrows><a href=?key=fouls>↑</a> <a href=?key=fouls_desc>↓</a></span></td>
	<td width=18%>Справедливость <span class=arrows><a href=?key=fair>↑</a> <a href=?key=fair_desc>↓</a></span></td>
	<td width=15%>Отношение <span class=arrows><a href=?key=attitude>↑</a> <a href=?key=attitude_desc>↓</a></span></td>
	<td width=13%>Сравнение <span class=arrows><a href=?key=compared>↑</a> <a href=?key=compared_desc>↓</a></span></td>
	<td width=15%>Итого <span class=arrows><a href=?key=total>↑</a> <a href=?key=total_desc>↓</a></span></td>
	</tr>";
   do 
    {
           printf ("<tr><td>%s</td><td>%.2f</td>
           <td>%.2f</td><td>%.2f</td><td>%.2f</td>
           <td>%.2f</td><td>%.2f</td>", 
		   
		   $myrow["receiver"], 
           ($myrow["rules"] ? $myrow["rules"] : "-"), 
		   ($myrow["fouls"] ? $myrow["fouls"] : "-"), 
		   ($myrow["fair"] ? $myrow["fair"] : "-"), 
		   ($myrow["attitude"] ? $myrow["attitude"] : "-"), 
		   ($myrow["compared"] ? $myrow["compared"] : "-"), 
		   ($myrow["rules"] & $myrow["fouls"] & $myrow["fair"] & $myrow["attitude"] & $myrow["compared"] ? $myrow["rules"] + $myrow["fouls"] + $myrow["fair"] + $myrow["attitude"] + $myrow["compared"] : "-")	//TBD 
		   );
    }
    while ($myrow = $result->fetch_array(MYSQL_ASSOC));
    print "</table>";
}
else
{
        echo "таблица пуста";
        exit();
}











?>

		</div>
