function validateForm()
{
	var errorMsg = "";
	var nameValue=document.forms["register"]["name"].value;
	if (nameValue==null || nameValue=="")
	{
	  //errorMsg = "Name not specified";
	}

	var clubValue=document.forms["register"]["club"].value;
	var otherclubValue=document.forms["register"]["otherclub"].value;
	if (clubValue=="" && (otherclubValue=="" || otherclubValue==null))
	{
		if(errorMsg != "")
		{
			errorMsg = errorMsg + "\n";
		}
		errorMsg = errorMsg + "Club name not specified";	
	}

	if(errorMsg != "")
	{
		alert(errorMsg);
		return false;
	}

    return true;
}

function toggleShowCity()
{
	var divCity = document.getElementById('otherCity');
	if(document.register.isnotlocal.checked == true)
	{
		divCity.style.display = 'block';
	}
	else
	{
		divCity.style.display = 'none';
	}
}

function toggleShowClub()
{
	var divClub_name = document.getElementById('Club_name');
	if(document.register.club.value == '')
	{
		divClub_name.style.display = 'block';
	}
	else
	{
		divClub_name.style.display = 'none';
	}
}
/*
function validateteamform()
{

	var divTeam_name = document.getElementById('otherclub');
	if (nameValue==null || nameValue=="")
	{
	  alert("Club name not specified");
	  return false;
	}
    return true;
}
*/