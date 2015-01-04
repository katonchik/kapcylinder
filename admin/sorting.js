	
	var ajax_url = 'ajax.php';
	var playersArray = [];

	function Team(teamName) {
	}

	function putPlayersIntoArray()
	{
		var playersArr = [];
		var team_arr = $( "ul.team" );
		team_arr.each(function()
		{
			var ulid = $(this).attr("id");
			var team_idt = ulid.substr(1);
			var players_arr = $(this).children("li");
			players_arr.each(function()
			{
				var liid = $(this).attr("id");
				var player_idt = liid.substr(1);
				var player_str = player_idt + "::" + team_idt;
				playersArr.push(player_str);
			});

		});
		return playersArr;
	}

	$(function()
	{
		
		$( "ul.team" ).sortable({
			connectWith: "ul"
		});

		$( "ul.team" ).disableSelection();

		$('#submit').click(function(){
			playersArray = putPlayersIntoArray();
			var json_str = $.toJSON(playersArray);
			var ajax_res = false;
			$.ajax({
			  type: 'POST',
			  url:  ajax_url,
			  data: {data: json_str},
			  cache: false,
			  async: false,
			  success: function(res) {
				 ajax_res = true;
			  }
			});
			return ajax_res;
		});//end save
 
	});
