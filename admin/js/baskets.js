/**
 * Created by Katon on 27.12.2014.
 */

window.onload = function() {
    document.getElementById("distributor").addEventListener("dragover", function(ev) {
        console.log("dragover");
        if (ev.target && ev.target.classList.contains("playerList")) {
            if (ev.preventDefault){
                ev.preventDefault();
            }
        }
    });

    document.getElementById("distributor").addEventListener("dragenter", function(ev) {
        console.log("dragenter");
        if (ev.target.classlist && ev.target.classList.contains("playerList")) {
            ev.target.style.border = '1px dashed black';
        }
    });

    document.getElementById("distributor").addEventListener("dragleave", function(ev) {
        console.log("dragleave");
        if (ev.target.classlist && ev.target.classList.contains("playerList")) {
            ev.target.style.border = 'none';
        }
    });

    document.getElementById("distributor").addEventListener("drop", function(ev) {
        console.log("drop");
        if (ev.target && ev.target.classList.contains("playerList")) {
            if (ev.preventDefault){
                ev.preventDefault();
            }
            ev.target.style.border = 'none';
            var playerID = ev.dataTransfer.getData("text");
            console.log("recovering id: " + playerID);
            ev.target.appendChild(document.getElementById('player' + playerID));
            var basket = ev.target.getAttribute('data-basket');
            updatePlayerBasket(playerID, basket);
        }
    });

    document.getElementById("distributor").addEventListener("dragstart", function(ev) {
        console.log("dragstart");
        if (ev.target && ev.target.classList.contains("player")) {
            ev.dataTransfer.setData("text", ev.target.getAttribute('data-playerID'));
            //alert("player clicked");
            console.log("saving id: " + ev.target.id);
        }
    });

    function updatePlayerBasket(playerID, basket){

        //alert($(this).text());
        $.ajax({
            url: 'basket_ajax.php',
            data: {
                'player_id': playerID,
                'basket' : basket
            },
            dataType: 'json',
            success: function(data){

                if(data.successful)
                {
                    //alert('Success!');
                    //$currentLink.parent().parent().text(basket);
                    console.log("Player " + playerID + " moved to basket " + basket);
                }
                else
                {
                    //alert('Error');
                    //$currentLink.parent().parent().text(basket);
                    console.log("Failed to move player " + playerID + " to basket " + basket + ": " + data.msg);
                }

            },
            error: function(data){
                //alert('Error!');
                console.log("Failed to move player " + playerID + " to basket " + basket);
            }
        });

        return false;

    }

};