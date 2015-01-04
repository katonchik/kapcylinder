/**
 * Created by Katon on 26.12.2014.
 */

$(document).ready(function () {
    var events = $('#events'),
        slider = $('#jqxSlider'),
        milestone = slider.attr('data-milestone'),
        milestone_descr;

    function displayEvent(event) {
        milestone = event.args.value;
        milestone_descr = $('#milestone_' + milestone).html();
        events.jqxPanel('clearContent');
        events.jqxPanel('prepend', '<div class="item" style="margin-top: 5px;">' + milestone_descr + '</div>');
    }

    function saveMilestone(event) {
        milestone = event.args.value;

        //alert($(this).text());
        $.ajax({
            url: 'milestone_ajax.php',
            data: {
                'milestone': milestone
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    //do nothing
                }
                else
                {
                    alert('Error. Please contact Katon.');
                }

            },
            error: function(data){
                alert('Error. Please contact Katon.');
            }
        });

        return false;

    }

    events.jqxPanel({  height: '150px', width: '600px' });
    $('#jqxSlider div').css('margin', '5px');
    //change event
    slider.jqxSlider({
        width: 600,
        max: 4,
        ticksFrequency: 1,
        mode: 'fixed',
        tooltip: true,
        tooltipHideDelay: 5000,
        value: milestone
    });
    milestone_descr = $('#milestone_' + milestone).text();
    events.jqxPanel('prepend', '<div class="item" style="margin-top: 5px;">' + milestone_descr + '</div>');

    slider.on('change', function (event) {
        displayEvent(event);
        saveMilestone(event);
    });
});
