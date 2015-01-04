$(function(){
    $("div[id^=question] .answer a").click(function(){
        var $currentLink = $(this);
        var q_id = $currentLink.parent().parent().attr('id');
        var new_value = $currentLink.attr('class');


        //alert($(this).text());
        $.ajax({
            url: '../ajax.php',
            data: {
                'q_id': q_id,
                'new_value' : new_value
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    $currentLink
                        .parent().siblings('.question')
                        .text(data.msgText);
                    switch(q_id)
                    {
                        case 'question_accommodation':
                            $currentLink
                                .removeClass(data.old_class)
                                .addClass(data.new_class)
                                .text(data.linkText);
                            break;
                        case 'question_participation':
                            $currentLink.addClass('choosed');
                            $currentLink.siblings('a').removeClass('choosed');
                            var divIfRegistered = document.getElementById('ifRegistered');
							console.log(new_value);
							if(new_value == 'no') {
								divIfRegistered.style.display = 'none';
							}
							else {
								divIfRegistered.style.display = 'block';							
							}
                            //var divAccommodation = document.getElementById('accommodation_block');
                            //divAccommodation.style.display = 'block';
                            //alert(data.debugMsg);
                            break;
                        case 'question_division':
                        case 'question_tshirt':
                        case 'question_lunches':
                        default:
                            $currentLink.addClass('choosed');
                            $currentLink.siblings('a').removeClass('choosed');
                            break;
                    }

                }
                else
                {
                    alert('Error');
                }

            }
        });

        return false;
    });



    $("#saveTimes").click(function(){
        var $currentLink = $(this),
            arrival = $("#arrival").val(),
            departure = $("#departure").val();

        $.ajax({
            url: '../ajax.php',
            data: {
                'q_id' : 'saveTimes',
                'arrival' : arrival,
                'departure' : departure
            },
            dataType: 'json',
            success: function(data) {
                $currentLink.parent().siblings('#timesMessage').text(data.msgText);
            }
        });

        return false;
    });

	
	
	
	
	
    $(".preference select").change(function(){
        var $currentLink = $(this);
        var q_id = $currentLink.attr('id');
        var new_value = $currentLink.val();
        var new_name = $currentLink.find('option:selected').text();

        //alert($(this).text());
        $.ajax({
            url: '../ajax.php',
            data: {
                'q_id' : q_id,
                'new_value' : new_value,
                'new_name' : new_name
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    $currentLink
                        .parent().siblings('#preference_message')
                        .text(data.msgText);
                }
                else
                {
                    alert('Error');
                }

            }
        });

        return false;
    });
	
	
	
	
	
	
	
	
	
});

