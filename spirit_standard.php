<h3 class="page_title">Дух игры: <a href="<?php echo SITE_URL; ?>/spirit/">Сводная статистика</a> / Стандарт / <a href="<?php echo SITE_URL; ?>/spirit_easy/">Easy level</a> / <a href="<?php echo SITE_URL; ?>/spirit_centered/">Центрированный</a></h3>

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
	$key = "date";
}
if (isset($key) && (in_array($key, $s) || in_array(str_replace (" DESC", "", $key), $s)))
{
	$result = $mysqli->query("
	SELECT time as date, t1.name as appraiser, t2.name as receiver, s2.rules, s2.fouls, s2.fair, s2.attitude, s2.compared, s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared as total,
		concat(p1.firstname, '<br>', p1.lastname) AS  mvp, concat(p2.firstname, '<br>', p2.lastname) AS  msp, concat(p3.firstname, '<br>', p3.lastname) AS  mpp
	FROM `uo_spirit_my` as s1, `uo_spirit_my` as s2, `uo_team` as t1, `uo_team` as t2, `uo_game`, `uo_player` as p1, `uo_player` as p2, `uo_player` as p3
	WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s1.receiver = t1.team_id AND s2.receiver = t2.team_id AND s1.game_id=uo_game.game_id AND s2.mvp=p1.player_id AND s2.msp=p2.player_id AND s2.mpp=p3.player_id AND s2.receiver < 1017
	ORDER BY ".$mysqli->real_escape_string(htmlspecialchars($key)).", date");
	
	/*$i = 1;
	$result = $mysqli->query("
	SELECT t$i.name as receiver, AVG(s2.rules) AS rules, AVG(s2.fouls) AS fouls, AVG(s2.fair) AS fair, AVG(s2.attitude) as attitude, AVG(s2.compared) as compared, AVG(s2.rules+s2.fouls+s2.fair+s2.attitude+s2.compared) as total, s2.mvp, s2.msp, s2.mpp
	FROM `uo_spirit_my` as s1, `uo_spirit_my` as s2, `uo_team` as t1, `uo_team` as t2
	WHERE s1.receiver <> s2.receiver AND  s1.game_id = s2.game_id AND s1.receiver = t1.team_id AND s2.receiver = t2.team_id
	GROUP BY t$i.name
	ORDER BY ".mysql_escape_string($key), $link);
	//", $link);
	//ORDER BY ".mysql_escape_string(htmlspecialchars($key)), $link);*/
        if(!$result) {echo "неудачный запрос";exit();}
}
else exit("неверный формат запроса!");
if ($result->num_rows > 0)
{
        $myrow = $result->fetch_array(MYSQL_ASSOC);
	print "<table width=100% class=spirit>
	<tr class=headers>
	<td>Дата <span class=arrows><a href=?key=date>↑</a> <a href=?key=date_desc>↓</a></span></td>
	<td>Выставили <span class=arrows><a href=?key=appraiser>↑</a> <a href=?key=appraiser_desc>↓</a></span></td>
	<td>Получили <span class=arrows><a href=?key=receiver>↑</a> <a href=?key=receiver_desc>↓</a></span></td>
	<td>Прав. <span class=arrows><a href=?key=rules>↑</a> <a href=?key=rules_desc>↓</a></span></td>
	<td>Фолы <span class=arrows><a href=?key=fouls>↑</a> <a href=?key=fouls_desc>↓</a></span></td>
	<td>Справ. <span class=arrows><a href=?key=fair>↑</a> <a href=?key=fair_desc>↓</a></span></td>
	<td>Отнош. <span class=arrows><a href=?key=attitude>↑</a> <a href=?key=attitude_desc>↓</a></span></td>
	<td>Сравн. <span class=arrows><a href=?key=compared>↑</a> <a href=?key=compared_desc>↓</a></span></td>
	<td>Итого <span class=arrows><a href=?key=total>↑</a> <a href=?key=total_desc>↓</a></span></td>
	<td>MVP <span class=arrows><a href=?key=mvp>↑</a> <a href=?key=mvp_desc>↓</a></span></td>
	<td>MSP <span class=arrows><a href=?key=msp>↑</a> <a href=?key=msp_desc>↓</a></span></td>
	<td>MPP <span class=arrows><a href=?key=mpp>↑</a> <a href=?key=mpp_desc>↓</a></span></td>
	</tr>";
   do 
    {
           printf ("<tr><td>%s</td>
           <td>%s</td><td>%s</td><td class=results>%d</td>
           <td class=results>%d</td><td class=results>%d</td><td class=results>%d</td>
           <td class=results>%d</td><td class=sum>%d</td><td>%s</td>
           <td>%s</td><td>%s</td></tr>", 
		   
		   str_replace("2013-", "2013-<br>", substr($myrow["date"], 0, strpos($myrow["date"], " "))), 
		   str_replace(" (", "<br>(", $myrow["appraiser"]),
		   str_replace(" (", "<br>(", $myrow["receiver"]), 
		   ($myrow["rules"] ? $myrow["rules"] - 1 : "-"), 
		   ($myrow["fouls"] ? $myrow["fouls"] - 1 : "-"), 
		   ($myrow["fair"] ? $myrow["fair"] - 1 : "-"), 
		   ($myrow["attitude"] ? $myrow["attitude"] - 1 : "-"), 
		   ($myrow["compared"] ? $myrow["compared"] - 1 : "-"), 
		   (1 ? $myrow["rules"] + $myrow["fouls"] + $myrow["fair"] + $myrow["attitude"] + $myrow["compared"] - 5 : "-"),	//TBD	   
		   ($myrow["mvp"] ? $myrow["mvp"] : "-"), 
		   ($myrow["msp"] ? $myrow["msp"] : "-"), 
		   ($myrow["mpp"] ? $myrow["mpp"] : "-") 
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
