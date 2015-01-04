<?php
	define('DEBUG', false);
	define('LOGLEVEL', 0);
	
	function getTotalPlayers($array)
	{
		$totalCount = 0;
		foreach($array as $playerGroup)
		{
			$totalCount += count($playerGroup->players);
		}
		return $totalCount;
	}
	
	function renderPlayerTotals($baskets, $teams, $context)
	{
		if(LOGLEVEL > 0)
		{
			$inBaskets = getTotalPlayers($baskets);
			$inTeams =  getTotalPlayers($teams);
			$total = $inBaskets + $inTeams;
			echo "<div style=\"background-color: lightyellow;\">";
			echo $context . ". ";
			echo "Total = {$total} ({$inBaskets} in baskets - {$inTeams} in teams)";
			echo "</div>";
		}	
	}
	
	function renderVisual($teams, $class)
	{
		if(LOGLEVEL > 0)
		{
			echo "<div style=\"overflow:hidden\">";
				foreach ($teams as $team)
				{
					$team->render($class);
				}
			echo "</div>";
		}		
	}
	
	class PlayerGroup {
		public $name;
		public $players = array();

		public function debugListPlayers($type="", $comment="")
		{
			$playerCount = count($this->players);
//	if(DEBUG == true) echo "{$type} {$this->name} content {$comment} <br />";
			for($i=0; $i<$playerCount; $i++)
			{
				if($this->players[$i])
				{
					$player = $this->players[$i];
					echo "{$i} of {$playerCount}: {$player->name} ({$player->id}) <br />";
				}
				else
				{
					echo "<span style=\"color: gray\">{$i} of {$playerCount}: Element missing</span><br />";
				}
			}
			echo "<br /><br />";
		}

		public function render($class="")
		{
			$position = 1;
			echo '<div class="team ' . $class . '">';
			echo '<h3 class="team__name">' . $this->name . ' (' . count($this->players) . ')</h3>';
			$playerCount = count($this->players);
	//		echo $playerCount . " players<br />";
			for($i=0; $i<$playerCount; $i++) //each($team->players as $player)
			{
				if(isset($this->players[$i]))
				{
					$player = $this->players[$i];
					//echo '<span style="color:'.$teamPlayer->color . '">' . $player->basket . ": " . $teamPlayer->name . "</span><br />";
					//echo '<span style="color:'.$teamPlayer->color . '">' . $teamPlayer->teamRating . ": " . $teamPlayer->name . "</span><br />";
					$link = $player->makePlayerLink();
					if(isset($player->position))
					{
						echo "<div class=\"team__player {$player->sex}\">{$player->position}: {$link}</div>";
					}
					elseif($player)
					{
						echo "<div class=\"team__player {$player->sex}\">{$link}</div>";					
					}
					else
					{
						echo "<div class=\"team__player\">-</div>";
					}
					//echo  $teamPlayer->name . "<br />";
				}
				else
				{
					//echo '&lt;missing player&gt;<br />';
				}
				
				$position++;
			}
			if($this->link && $this->link != 'http://')
			{
				echo '<div class="team__link"><a href="' . $this->link . '" target="_blank" style="text-decoration:none;">&gt;&gt;&gt;</a></div>';
			}
			echo '</div>';
			
		}
	}
	
	
	class Basket extends PlayerGroup {
		public function __construct($name)
		{
			$this->name = $name;
		}
		public function put($player)
		{
			$this->players[] = $player;
		}

		public function drawLiked($isOdd, $team)
		{
			
			//$wantedArr = $team->getWanted();
			//$unwantedArr = $team->getUnwanted();
			$teamPlayers = $team->players;
			
if(DEBUG == true) echo "<div style=\"margin-left: 20px;\"><strong>Drawing liked from basket {$this->name} for team {$team->name}</strong><br />";
//	if(DEBUG == true) echo "Checking if basket [" . $this->name . "] has players wanted by the team already assembled. Looping through the basket.<br />";
//			echo "wantedArr = " . print_r($wantedArr) . ";<br />UnwantedArr = " . print_r($unwantedArr) . "<br />";
			$basketSize = count($this->players);
			$teamSize = count($teamPlayers);
if(DEBUG == true) echo "<strong>Basket " . $this->name . " size: " . $basketSize . "</strong><br />";
//if(DEBUG == true) echo "Team " . $team->name . " size: " . $teamSize . "; content: " . print_r($team->players) . "<br />";
//if(DEBUG == true) echo "Looping through " . $this->name . " basket players. <br />";
			for($i=0; $i<$basketSize; $i++)
			{
				$basketPlayer = $this->players[$i];
//if(DEBUG == true) echo "<br />Searching for a special for team " . $team->name . ". Looking at <strong>" . $basketPlayer->name . "</strong><br/ >";
//if(DEBUG == true) echo "Check if team " . $team->name . " has special likes for basket player " . $basketPlayer->name . "<br />";

				//First, check if this basketPlayer is disliked by the team or if he dislikes somebody on the team.
				//If so, go to the next basket player.
				$isBasketPlayerSuitable = true;
				for($j=0; $j<$teamSize; $j++)
				{
					if(isset($team->players[$j]))
					{
						$teamPlayer = $team->players[$j];
						if(!$teamPlayer) //How is this happening?
						{
	if(DEBUG == true) $team->debugListPlayers('Team', ' - failed to find player {$j} in the team array when searching for a liked');
							continue; //
						}
//	if(DEBUG == true) echo "Check if {$basketPlayer->name} [{$i} of {$basketSize}] from the basket and {$teamPlayer->name} [{$j} of {$teamSize}] from the team have mutual dislike.<br />";
						if($teamPlayer->dislike == $basketPlayer->id || $basketPlayer->dislike == $teamPlayer->id)
						{
	if(DEBUG == true) echo "Appears that " . $basketPlayer->name . " or " . $teamPlayer->name . " has a mutual dislike. Marking " . $basketPlayer->name . " as not suitable.<br />";
							$isBasketPlayerSuitable = false;
							break; //go to the next player in the basket
						}
					}
				}
				if(!$isBasketPlayerSuitable)
				{
if(DEBUG == true) echo $basketPlayer->name . " is not suitable for team " . $team->name . ". Moving on to the next basket player.<br />";
					continue;
				}
				
//if(DEBUG == true) echo $basketPlayer->name . " has no problems with " . $team->name . ". Looking for special likes.<br />";
				//If we are here, the player is not disliked.
				//Let's check if this basketPlayer has a special like to somebody on the team or somebody on the team has a special liking for him. 
				for($j=0; $j<$teamSize; $j++)
				{
					if(isset($teamPlayers[$j]))
					{
						$teamPlayer = $teamPlayers[$j];
//	if(DEBUG == true) echo "Checking if " . $basketPlayer->name . " has a special like with " . $teamPlayer->name . ".<br />";
						if(!$teamPlayer) //How is this happening?
						{
							continue; //
						}
						if($teamPlayer->like == $basketPlayer->id || $basketPlayer->like == $teamPlayer->id )
						{
							//Found a special like. Retrieving.
							$tmp_arr = array_splice($this->players, $i, 1);
							$selectedPlayer = $tmp_arr[0];
							$selectedPlayer->color = "green";
if(DEBUG == true) echo "<span style=\"color:red;\">{$selectedPlayer->name} has a special like for {$teamPlayer->name} in team {$team->name}. {$selectedPlayer->name} taken from bakset {$this->name}.  Remaining players: " . count($this->players) . ". </span><br/></div>";
							return $selectedPlayer;
						}
					}
				}
//if(DEBUG == true) echo $basketPlayer->name . " has no special likes for team " . $team->name . "<br/>";
			}
if(DEBUG == true) echo "<strong>No specials found for team " . $team->name . "</strong><br/></div>";
			return false; //preferred player not found
		}

		public function drawForDisliked($isOdd, $team, $allUnwantedIDs) //Why do we need allUnwanted IDs?  Rewriting... 30.12.2014
		{
if(LOGLEVEL > 2) "Drawing from basket " . $this->name . " for disliked team " . $team->name . "<br />";
			//$teamUnwantedIDs = $team->getUnwantedIDs($allUnwantedIDs);

			$basketSize = count($this->players);
			for($i=0; $i<$basketSize; $i++)
			{
				if($this->players[$i])
				{
					$basketPlayer = $this->players[$i];
	//echo "Maybe " . $basketPlayer->name . "?  The dislike id is: " . $basketPlayer->dislike . "<br />";
	//				echo "debug on Neutral: Player = " . $player->name . "<br />";
	//Check if this basket player is okay with all team players
	//				if(!in_array($basketPlayer->dislike, $teamUnwantedIDs))
	//				{
					$teamSize = count($team->players);
					$isTeamDisliked = false;
					for($j=0; $j<$teamSize; $j++)
					{
						if(isset($team->players[$j]))
						{
							$teamPlayer = $team->players[$j];
							
							if($basketPlayer->dislike == $teamPlayer->id)
							{
								$isTeamDisliked = true;
								break;
							}
						}
					}
					
if(LOGLEVEL > 4) echo "Potential candidate found among generally unwanted in basket {$this->name} for team {$team->name}. Basket size: " . count($this->players) . "<br />";
					if(!$isTeamDisliked)
					{
						$tmp_arr = array_splice($this->players, $i, 1);
if(LOGLEVEL > 4) echo "Suitable candidate found among generally unwanted and removed from basket " . $this->name ." for team ". $team->name . ": " . $tmp_arr[0]->name . ". Basket size: " . count($this->players) . "<br />";
						return $tmp_arr[0];
					}
				}
				else
				{
if(DEBUG == true) echo "Basket {$this->name} is missing player {$i} when drawing for team {$team->name}.<br />";
				}
			}


			
/*		
			$unwantedArr = $team->getDislikeIDs(); //get IDs of players unwanted by this team
			
//echo "UnwantedIDs: " . print_r($unwantedArr) . "<br />";
			//if we are here, there are no preferred matches.
			//Let's look for a neutral match
			$basketSize = count($this->players);
			for($i=0; $i<$basketSize; $i++)
			{
				$basketPlayer = $this->players[$i];
//echo "Maybe " . $basketPlayer->name . "?  The dislike id is: " . $basketPlayer->dislike . "<br />";
//				echo "debug on Neutral: Player = " . $player->name . "<br />";
				//Check if this basket player is okay with all team players
				if(!in_array($basketPlayer->dislike, $unwantedArr))
				{
					$tmp_arr = array_splice($this->players, $i, 1);
					echo "Suitable candidate found among generally unwanted in basket " . $this->name ." for team ". $team->name . ": " . $tmp_arr[0]->name . "<br />";
					return $tmp_arr[0];
				}
			}
//			echo "!!!!!!!!!!!!!!!!!!!Failed to get Neutral: Looping through basket " . $this->name . "<br />";
*/		
			//if we are here, well... there are no neutral matches left.
			//Let's pick the first unwanted match

			for($i=0; $i<$basketSize; $i++)
			{
				$basketPlayer = $this->players[$i];
				$tmp_arr = array_splice($this->players, $i, 1);
				$selected_player = $tmp_arr[0];
				$selected_player->color = "orange";
				return $selected_player;
			}
		

/*
			if($is_odd)
			{
				return array_shift($this->players);
			}
			else
			{
				return array_pop($this->players);
			}
*/
			//do the main logic here to pick a player
			//assign the player into a variable
			//delete the player from the array
			//return the player

			//What if two people from the same basket want the same partner?
			
			return false;

		}

		public function drawRegular($isOdd)
		{
		//	echo "<strong>debug: drawRegular. basket " . $this->name . " </strong><br />";
			//if we are here, there are no preferred and, hopefully, disliked matches.
			//Let's look for a neutral match
			$playerCount = count($this->players);
			for($i=0; $i<$playerCount; $i++)
			{
				if($this->players[$i])
				{
					$this->players[$i]->color = "brown";
					$oneElementArray = array_splice($this->players, $i, 1);
					return $oneElementArray[0];
				}
			}
			

		}
		
		public function getUnwanted($allUnwantedIDs)
		{
			$unwanted = array();
			$playerCount = count($this->players);
if(DEBUG == true) $this->debugListPlayers("Basket", " before getting unwanted");
if(LOGLEVEL > 4) echo "Basket size before taking unwanted : " . count($this->players) . "<br />";
			for($i=0; $i<$playerCount; $i++)
			{
				//If we reduce (splice) the array while looping through it, so we'll get the Undefined offset error
				if(isset($this->players[$i]))
				{
					$player = $this->players[$i];
					if(in_array($player->id, $allUnwantedIDs))
					{
						$tmp_arr = array_splice($this->players, $i, 1); 
if(LOGLEVEL > 4) echo "Unwanted found and removed: " . $tmp_arr[0]->name . "<br />";
						$unwanted[] = $tmp_arr[0];
					}
				}
			}
if(LOGLEVEL > 4) echo "Basket size after taking unwanted : " . count($this->players) . "<br />";
			return $unwanted;
		}
		
		public function hasDislikers($team)
		{
//			echo "Looking for players with dislikes on team " . $team->name ."<br />";
			$basketPlayerCount = count($this->players);
			$teamPlayerCount = count($team->players);
 			for($i=0; $i<$basketPlayerCount; $i++)
			{
				if($this->players[$i])
				{
					$basketPlayer = $this->players[$i];

					//3.3.2. Loop through team players
					for($j=0; $j<$teamPlayerCount; $j++)
					{
						//3.3.3. If the basket player dislikes the team player
						if(isset($team->players[$j]))
						{
							$teamPlayer = $team->players[$j];
							if(!$teamPlayer) //Why is this happening?
							{
								return false;
							}			
							if($basketPlayer->dislike 
								== $teamPlayer->id)
							{
								return true;
							}
						}
					}
				}
			}
			return false;
		}

	}
	
	class Team extends PlayerGroup {
		public $color;
		public $link;

		public function __construct($name, $color, $link)
		{
			$this->name = $name;
			$this->color = $color;
			$this->link = $link;
		}

		/**
		* Returns list of ids of players wanted by this team
		*/
		public function getWanted(){
			$wanted = array();
			foreach ($this->players as $player)
			{
				if(isset($player->like))
				{
					$wanted[] = $player->like;
				}
			}
			return $wanted;
		}

		/**
		* Returns list of ids of players unwanted by this team
		*/
		public function getDislikeIDs(){
			$unwanted = array();
			foreach ($this->players as $player)
			{
				if(isset($player->dislike))
				{
					$unwanted[] = $player->dislike;
				}
			}
			return $unwanted;
		}

		/**
		* Returns list of ids of players on this team
		*/
		public function getAllocated(){
			$allocated = array();
			foreach ($this->players as $player)
			{
				$allocated[] = $player;
			}
			return $allocated;
		}
		
		public function dislikes($basketPlayer){
			$strangeID = $basketPlayer->id;
			foreach ($this->players as $teamPlayer)
			{
				if($teamPlayer->dislike == $basketPlayer->id OR $basketPlayer->dislike == $teamPlayer->id)
				{
					return true;
				}
			}
			return false;
			
		}
		
		/**
		* Returns ids of unwanted players on the team
		*/
		public function getUnwantedIDs($allUnwantedIDs)
		{
			$unwanted = array();
			$playerCount = count($this->players);
			for($i=0; $i<$playerCount; $i++)
			{
				$player = $this->players[$i];
				if(in_array($player->id, $allUnwantedIDs))
				{
					$unwanted[] = $player->id;
				}
			}
			return $unwanted;
		}


		
	}
	

	$xtra = " AND participation = " . YES . " AND basket > 0";
	$playersArr = getTournamentPlayers($mysqli, $tournid, $xtra);
	
	$allUnwantedIDs = array();
	
	//Create empty baskets
	$baskets = array();
	for($i=0; $i<$settings['number_of_baskets']; $i++)
	{
		$baskets[] = new Basket($i+1);
	}
	
	//Populate baskets
	foreach($playersArr as $playerArr)
	{
		$player = new Player($playerArr);
		if($player->dislike)
		{
			$allUnwantedIDs[] = $player->dislike;
		}
		$players[] = $player;
		$playerBasketID = $playerArr['basket'];
		//echo "playerBasketID = " . $playerBasketID;
		$playerBasket = $baskets[$playerBasketID - 1];
		$playerBasket->put($player);
	}
	
	foreach ($baskets as $basket)
	{
		//echo print_r($basket->players) . "<br /><br />";
	}

	
	//Step 1. Get teams from DB
	$teams = array();
	$query = "SELECT * FROM hat_teams WHERE tournament_id = $tournid ORDER BY team_id";
	$result = $mysqli->query($query);
	while ($row = $result->fetch_assoc())
	{
		$team = new Team($row['team_name'], $row['color'], $row['link']);
		$teams[] = $team;
	}
	if(count($teams) == 0)
	{
		die("Please create teams first");
	}

	//Step 2. Distribute the first basket (index 0)
	$basket = $baskets[0];
	foreach($teams as $team)
	{
		$player  = array_shift($basket->players);
//		$player->teamRating = 0; //Creating default object from empty value ????
		$team->players[0] = $player;
	}
	
	//Step 3. Distribute other baskets
	$is_odd = true;
	for($j=1; $j<count($baskets); $j++)
	{
		$basket = $baskets[$j];
if(LOGLEVEL > 0) echo "<br /><h1>Doing Basket {$basket->name}. DISTRIBUTING PLAYERS INTO TEAMS. </h1><br/>";

		//Step 3.1. For each team, look for 'likes' in this given basket
		if($settings['enable_likes'])
		{
			foreach($teams as $team)
			{
	if(DEBUG == true) echo "<h2>Drawing likes for team {$team->name}</h2><br />";
				$player = $basket->drawLiked($is_odd, $team);
				if($player)
				{
					$player->teamRating = $j;
					$team->players[$j] = $player;
				}
			}
			renderVisual($teams, 'hat-team');
			renderVisual($baskets, 'basket');
if(DEBUG == true) echo $basket->debugListPlayers("Basket", "after drawing special") ."<br />";
renderPlayerTotals($baskets, $teams, 'After liked');
		}
		
		if($settings['enable_dislikes'])
		{


			
	if(LOGLEVEL > 2) echo "<h2>Distributing unliked from basket {$basket->name}</h2><br />";
			//Step 3.2. Look for 'dislikes' in this given basket and distribute them across teams
			//	echo count($basket->players) . " players in the basket (before unwanted)<br />";
			$basketUnwanted = $basket->getUnwanted($allUnwantedIDs);
			$basketUnwantedCount = count($basketUnwanted);
	if(LOGLEVEL > 3) echo "starting on unliked. basketUnwantedCount = $basketUnwantedCount<br />";
			$k = 0;
			$teamFound = false;
			foreach($basketUnwanted as $i => $basketPlayer)
			{
				//if(isset($basketUnwanted[$i]))
				//{
	if(LOGLEVEL > 3) echo "<strong>Looking to accommodate {$basketPlayer->name}.  i={$i}</strong><br />";
					foreach($teams as $team)
					{
	if(LOGLEVEL > 4) echo $basketPlayer->name . " found in unliked and suggested for team " . $team->name . "<br />";
		//				if(isset($team->players[$j])) echo "slot is taken by: " . $team->players[$j]->name . "<br />";
						if($team->dislikes($basketPlayer)) 
						{
	if(LOGLEVEL > 4) echo "Team " . $team->name . " dislikes: " . $basketPlayer->name . "<br />";
						}
						elseif(!isset($team->players[$j]))
						{
	if(LOGLEVEL > 4) echo "Candidate " . $basketPlayer->name . " accepted for " . $team->name . "<br />";
							$team->players[$j] = $basketPlayer;
							$tmp_arr = array_splice($basketUnwanted, $k, 1);
	if(LOGLEVEL > 4) echo "<span style=\"color: green\">{$basketPlayer->name} placed into team {$team->name}</span><br />";
							$player = $tmp_arr[0];
							$player->teamRating = $j;
							$player->color = 'red';
							$team->players[$j] = $player;
							$teamFound = true;
							break;
						}
						else
						{
	if(LOGLEVEL > 4) echo "Spot {$j} in team {$team->name} is taken by {$team->players[$j]->name}. Moving on to the next team. <br />";
						}
					}
					if($teamFound == false)
					{
	if(LOGLEVEL > 2) echo "Failed to accommodate {$basketPlayer->name} and will put back into the general pool. <br />";	
						$k++;
					}
				//}
				//else
				//{
	//if(LOGLEVEL > 4) echo "BasketUnwanted is missing player {$i} from the list<br />";			
				//}
			}
	//		echo "finishing on unliked<br />";

	if(LOGLEVEL > 2) echo "Putting " . count($basketUnwanted) . " players back into the basket - those that failed to allocate under unwanted. ";
			if(count($basketUnwanted))
			{
	if(LOGLEVEL > 2) echo "Failed to distribute all unwanted players <br />";
			}
			$basket->players = array_merge($basketUnwanted, $basket->players);

	//		echo "After drawing unliked  " . print_r($basket->players) ."<br />";
	//if(DEBUG == true) echo $basket->debugListPlayers("Basket", "after drawing unliked");



	renderVisual($teams, 'hat-team');
	renderVisual($baskets, 'basket');




			//Step 3.3. Look for teams that are disliked by players in this basket and find players for each of them first
			
			//3.3.1. Find teams that are disliked by players in this basket
			foreach($teams as $team)
			{
				if($basket->hasDislikers($team))
				{
	//echo "Basket " . $basket->name . ". Disliked team: " . $team->name . ". Looking a player for " . $team->name . "<br />";
					if(!isset($team->players[$j]) && count($basket->players) > 0)
					{
						//3.3.6. For each disliked team, look through players in the basket 
						//3.3.7. For each basket player, loop through team players, 
						//3.3.8. If the basket player has no dislikes to any of the team players, add basket player to the team
						$player = $basket->drawForDisliked($is_odd, $team, $allUnwantedIDs);
	//echo '<span style="color: red">Found ' . $player->name . " for " . $team->name . ". Adding. </span><br />";
						$player->teamRating = $j;
						$player->color = 'blue';
						$team->players[$j] = $player;
					}
					else
					{
	//echo "Skipping, because spot $j is taken by " . $team->players[$j]->name . "<br />";				
					}
				}
	//			echo "After drawing into disliked " . $team->name . ": " . print_r($team->players) ."<br />";
	if(DEBUG == true) echo $basket->debugListPlayers("Basket", "after drawing into disliked team " . $team->name);
			}
			
			renderPlayerTotals($baskets, $teams, 'After disliked');
		}
		/*
		if(DEBUG == true)
		{	
			echo '<div style="color: green;">';
			echo "Before distributing regular players.<br />";
			foreach ($teams as $team)
			{
				echo "<br />" . $team->name . "<br />";
				foreach ($team->players as $teamPlayer)
				{
					echo $teamPlayer->teamRating . ": " . $teamPlayer->name . "</br >";
				}
				//After drawing unliked2  " . print_r($basket->players) ."<br />";
			}
			echo '</div><br />';
		}
		*/
	} //end if($settings['enable_disliked'])


	for($j=1; $j<count($baskets); $j++)
	{
		$basket = $baskets[$j];
if(LOGLEVEL > 0) echo "<br /><H1>Doing Basket {$basket->name}. DISTRIBUTING REGULAR PLAYERS INTO TEAMS.</h1><br/>";
		//Step 3.4. Distribute remaining players across remaining teams 

if(DEBUG == true) echo $basket->debugListPlayers("Basket", "before drawing regular");

		foreach($teams as $team)
		{
if(DEBUG == true) echo "Looking into team " . $team->name . "<br />";
//echo "J=$j<br />";			
			if(isset($team->players[$j]))
			{
if(DEBUG == true) echo "Slot $j is taken by " . $team->players[$j]->name . "<br />";
			}
			elseif(count($basket->players) > 0)
			{
if(DEBUG == true) echo "Slot $j is not yet taken. <br />";
				$player = $basket->drawRegular($is_odd);
//				$player->teamRating = $j;
				if(isset($player))
				{
if(DEBUG == true) echo "<span style=\"color:green\">Retrieved {$player->name}</span><br />";
					$team->players[$j] = $player;
				}
				else
				{
if(DEBUG == true) echo "<span style=\"color:red\">Failed to get player for slot {$j}:(</span><br />";					
				}
			}
			else
			{
if(DEBUG == true) echo "Slot $j is available, but the basket is empty.<br />";
			}
		}
		//echo "finishing on regular<br />";

		$is_odd = !$is_odd;

renderVisual($teams, 'hat-team');
renderVisual($baskets, 'basket');

renderPlayerTotals($baskets, $teams, 'After regular');

if(DEBUG == true) echo $basket->debugListPlayers("Basket", "after drawing regular");
	}


	
/* debug begin	
echo '<div style="color: blue;">';
echo "After distributing regular players.<br />";
foreach ($teams as $team)
{
	echo "<br />" . $team->name . "<br />";
	foreach ($team->players as $teamPlayer)
	{
		echo $teamPlayer->teamRating . ": " . $teamPlayer->name . "</br >";
	}
	//After drawing unliked2  " . print_r($basket->players) ."<br />";
}
echo '</div>';
debug end */

	
	//Show teams on screen
	$totalPlayers = 0;
	foreach ($teams as $team)
	{
		$team->render('hat-team');
		$totalPlayers += count($team->players);
	}
	

?>
