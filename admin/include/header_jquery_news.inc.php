	<script type="text/javascript">
		$(function(){
			$("a.publish").click(function(){
				var $currentLink = $(this);
				var el_id = $currentLink.attr('id');
				var partsArray = el_id.split('_');
				var status = partsArray[0];
				var news_id = partsArray[1];

				//alert($(this).text());
				$.ajax({
					url: 'news_ajax.php',
					data: {
						'news_id': news_id,
						'status' : status
					},
					dataType: 'json',
					success: function(data){

						if(data.successful)
						{
							//alert('Success!');
							$currentLink.children('img').attr('src', '../images/' + data.imgFile);
							$currentLink.attr('title', data.title);
							$currentLink.parent().parent().attr('class', data.rowClass);
							$currentLink.attr('id', data.idprefix + "_" + news_id);
						}
						else
						{
							//alert('Error');
							//$currentLink.children('img').attr('src', '../images/' + data.imgFile);
							//$currentLink.attr('id', data.idprefix + "_" + news_id);
						}
						
					},
					error: function(data){
						//alert('Error!');
						//$currentLink.children('img').attr('src', '../images/' + data.imgFile);
						//$currentLink.attr('id', data.idprefix + "_" + news_id);
					}
				});
				
				return false;
			});
	
		});
	</script>
	<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->
