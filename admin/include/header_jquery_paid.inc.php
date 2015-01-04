	<script type="text/javascript">
		$(function(){
			$(".paid input").change(function(){
				var $currentLink = $(this);
				var player_id = $currentLink.attr('id');
				var paid;
				if ($(this).is(":checked"))	{
					paid = 'y';
				}
				else {
					paid = 'n';
				}

				//alert($(this).text());
				$.ajax({
					url: 'payment_ajax.php',
					data: {
						'player_id': player_id,
						'paid' : paid
					},
					dataType: 'json',
					success: function(data){

						if(data.successful)
						{
							$currentLink.closest(".payment").fadeOut();
						}
						else
						{
							alert('Error');
						}
						
					},
					error: function(data){
						alert('Error!');
					}
				});
				
				return false;
			});
	
		});
	</script>
	<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->
