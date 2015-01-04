/**
 * Created by vkatolyk on 29.12.2014.
 */


    var searchInput = document.getElementById('searchName');
    console.log("inside onLoad");

    document.getElementById('searchBttn').addEventListener("click", function (){
        searchPlayer(searchInput.value);
    });

    searchInput.addEventListener("keypress", function (e){
        console.log("key pressed");
        var charCode = e.keyCode || e.which;
        if(charCode == 13) {
            searchPlayer(searchInput.value);
        }
    });

    function searchPlayer(searchStr)
    {
        console.log("Starting search");
        var httpRequest = new XMLHttpRequest();
        httpRequest.open("POST", "searchHelper.php");
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        httpRequest.send('searchStr=' + encodeURIComponent(searchStr));
        httpRequest.onreadystatechange = function(){
            if (httpRequest.readyState === 4) {
//                alert(httpRequest.responseText);
                // everything is good, the response is received
                var playerID = parseInt(httpRequest.responseText);
                if(playerID != 1 && !isNaN(playerID) ) {
                    window.location = "edit_player.php?id=" + httpRequest.responseText;
                }
            }
        }
    }
