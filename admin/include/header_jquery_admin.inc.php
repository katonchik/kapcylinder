	
	
	<script type="text/javascript"><!--
		function showcontrols(el_id, player_id){
			var controltext='<a href="">Will come</a> <a href="">Maybe</a> <a href="">Will not come</a>';
			document.getElementById(el_id).innerHTML=controltext;
		}
		function hidecontrols(el_id, orig_text){
			document.getElementById(el_id).innerHTML=orig_text;
		}
		//-->
	</script>


	<script type="text/javascript">
		$(function(){
			$("td[class^=participation]").hover(function(){
				var $container = $(this);
				$container.find('div.participation_info').css('display', 'none');
				$container.find('div.participation_controls').css('display', 'block');
			}, function(){
				var $container = $(this);
				$container.find('div.participation_info').css('display', 'block');
				$container.find('div.participation_controls').css('display', 'none');
			});
			
			
			$("div.participation_controls a").click(function(){
				var $currentLink = $(this);
				var el_id = $currentLink.attr('id');
				var partsArray = el_id.split('_');
				var participation = partsArray[0];
				var player_id = partsArray[1];

				//alert($(this).text());
				$.ajax({
					url: 'participation_ajax.php',
					data: {
						'player_id': player_id,
						'participation' : participation
					},
					dataType: 'json',
					success: function(data){

						if(data.successful)
						{
							//alert('Success!');
							$currentLink.parent().parent().text(participation);
						}
						else
						{
							//alert('Error');
							$currentLink.parent().parent().text(participation);
						}
						
					},
					error: function(data){
						//alert('Error!');
						$currentLink.parent().parent().text(participation);
					}
				});
				
				return false;
			});
	
		});
	</script>
	<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->
