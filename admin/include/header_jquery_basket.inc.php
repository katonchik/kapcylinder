	<script type="text/javascript">
		$(function(){
			$("td[class^=basket]").hover(function(){
				var $container = $(this);
				$container.find('div.basket_info').css('display', 'none');
				$container.find('div.basket_controls').css('display', 'block');
			}, function(){
				var $container = $(this);
				$container.find('div.basket_info').css('display', 'block');
				$container.find('div.basket_controls').css('display', 'none');
			});
			
			
			$("div.basket_controls a").click(function(){
				var $currentLink = $(this);
				var el_id = $currentLink.attr('id');
				var partsArray = el_id.split('_');
				var basket = partsArray[1];
				var player_id = partsArray[2];

				//alert($(this).text());
				$.ajax({
					url: 'basket_ajax.php',
					data: {
						'player_id': player_id,
						'basket' : basket
					},
					dataType: 'json',
					success: function(data){

						if(data.successful)
						{
							//alert('Success!');
							$currentLink.parent().parent().text(basket);
						}
						else
						{
							//alert('Error');
							$currentLink.parent().parent().text(basket);
						}
						
					},
					error: function(data){
						//alert('Error!');
						$currentLink.parent().parent().text(basket);
					}
				});
				
				return false;
			});
	
		});
	</script>
	<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->
