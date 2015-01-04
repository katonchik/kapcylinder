<?php
	class Player {
		public $id;
		public $name;
		public $like;
		public $dislike;
		public $basket;
		public $sex;
		public $uid;
		public $fb_user;
		public $photo;
		public function __construct($playerArr)
		{
			$this->id = $playerArr['id'];
			$this->name = $playerArr['name'];
			$this->like = $playerArr['like'];
			$this->dislike = $playerArr['dislike'];
			$this->basket = $playerArr['basket'];
			$this->sex = $playerArr['sex'];
			$this->uid = $playerArr['uid'];
			$this->fb_user = $playerArr['fb_user'];
			$this->photo = $playerArr['photo'];
		}
		
		public function makePlayerLink($showPhoto=0)
		{
			if($showPhoto)
			{
				$class = ' class="personPopupTrigger" ';
			}
			else
			{
				$class = '';
			}
			
			if(isset($this->uid) && $this->uid && $this->uid != 'NULL')
			{
				return "<a {$class} href=\"http://vk.com/id{$this->uid}\" target=\"_blank\">{$this->name}</a>\r\n";
			}
			else if(isset($this->fb_user) && $this->fb_user != "")
			{
				return "<a {$class} href=\"https://facebook.com/{$this->fb_user}\" target=\"_blank\">{$this->name}</a>\r\n";
			}
			else if($showPhoto && isset($this->photo) && $this->photo != "")
			{
				return "<a {$class} href=\"#\" >{$this->name}</a>\r\n";
			}
			else
			{
				return $this->name;
			}		
		}
		
		public function makeAdminLink()
		{
			if(isset($this->uid) && $this->uid && $this->uid != 'NULL')
			{
				$vk_img_link = '<a href="http://vk.com/id' . $this->uid . '" target="_blank"><img src="../_images/vk_logo.png" /></a>';
			}
			else
			{
				$vk_img_link = '<img src="../_images/vk_logo_off.png" />';
			}
			return $vk_img_link . ' <a href="edit_player.php?id=' . $this->id . '" class="' . $this->sex . '">' . $this->name . "</a>\r\n";
		}
		
		public function getLikeCount($mysqli, $tournid)
		{
			$likeCount = 0;
			$allTournamentPlayers = getTournamentPlayers($mysqli, $tournid, " AND participation=" . YES . " ORDER BY paid DESC, id");
			foreach($allTournamentPlayers as $player)
			{
				if($player['like'] == $this->id)
				{
					$likeCount++;
				}
			}
			return $likeCount;
		}

		public function getDislikeCount($mysqli, $tournid)
		{
			$dislikeCount = 0;
			$allTournamentPlayers = getTournamentPlayers($mysqli, $tournid, " AND participation=" . YES . " ORDER BY paid DESC, id");
			foreach($allTournamentPlayers as $player)
			{
				if($player['dislike'] == $this->id)
				{
					$dislikeCount++;
				}
			}
			return $dislikeCount;
		}
		
	}
?>