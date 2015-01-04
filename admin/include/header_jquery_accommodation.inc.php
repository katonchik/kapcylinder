	<script type="text/javascript">
		$(function(){
			$(".accommodation_info a").click(function(){
				var $currentLink = $(this);
				var el_id = $currentLink.attr('id');
				var partsArray = el_id.split('_');
				var accommodation = partsArray[0];
				var player_id = partsArray[1];

				//alert($(this).text());
				$.ajax({
					url: 'accommodation_ajax.php',
					data: {
						'player_id': player_id,
						'accommodation' : accommodation
					},
					dataType: 'json',
					success: function(data){

						if(data.successful)
						{
							//alert('Success!');
							$currentLink.text(data.linkText);
							$currentLink.attr('id', data.idprefix + "_" + player_id);
						}
						else
						{
							//alert('Error');
							$currentLink.text(data.linkText);
							$currentLink.attr('id', data.idprefix + "_" + player_id);
						}
						
					},
					error: function(data){
						//alert('Error!');
						$currentLink.text(data.linkText);
						$currentLink.attr('id', data.idprefix + "_" + player_id);
					}
				});
				
				return false;
			});
	
		});
	</script>
	<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->
