$(function(){
	//This works only inside top level function
/*
	$("div#account_links a.openForm").click(function(){
		alert("openForm jquery")
		var currentLink = $(this);
		var formContainerId = currentLink.attr('id');
		var tmp = formContainerId.split("_");
		//alert('opening form ' + tmp[0]);
		form = new FormPopup(tmp[0]);
		form.open();
		return false;
	});
*/
	$('div.lightbox input.submit').click(submitForm);
	//$('div#lightbox .submit').on("click", submitForm); //х3
	//$('div#lightbox .submit').live("click", submitForm); //1.3 //undefined is not a function
	//$(document).delegate('div#lightbox .submit', "click", submitForm);//1.4.3 //undefined is not a function
	//$(document).on("click", 'div#lightbox .submit', submitForm);//1.7+ ////undefined is not a function
	//$('#mySubmit').bind("click", submitForm);



	$('div.lightbox input.submit').click(function() {
		submitForm($(this));
	});

/*
	$('div.lightbox .submitter').click(function() {
		alert(".submitter clicked");
	});


	$('div.lightbox input').on("click", function() {
		alert("input clicked");
	});

	$('div.lightbox form').on("click", function() {
		alert("form clicked");
	});

	$('div.lightbox form').click(function() {
		alert("form clicked");
	});


	//this one works!!!
	$('div.lightbox').click(function() {
		alert("div clicked");
	});

*/


	$('div.lightbox input.submit').keypress(function () {
		alert("key pressed");
		if (window.event.keyCode == '13') {
			submitForm($(this));
		}
	});

	var submitForm = function (currentButton) {
		var action_id = currentButton.parent().parent().attr('id'); //this is div id, e.g.: "changePassword"
		var form_inputs = currentButton.siblings(); //array of inputs
		var submitted_data = {action_id: action_id};
		for (var i = 0; i < form_inputs.length; i++) {
			var key, value;
			if(form_inputs[i].tagName.toLowerCase() == "div") //hack to extract stuff from the jsx combo box
			{
				//key = form_inputs[i].getElementsByTagName("select")[0].name;
				key = form_inputs[i].id.split("_")[1];
				value = form_inputs[i].getElementsByTagName('input')[0].value;
				//value = form_inputs[i].getElementsByClassName("scombobox-value")[0].value;
				//alert(form_inputs[i].tagName + ": " + key + " = " + value);
			}
			else
			{
				key = form_inputs[i].name;
				value = form_inputs[i].value;
			}
			if (typeof(key) != "undefined") {
				submitted_data[key] = value;
			}
		}

		//alert($(this).text());
		$.ajax({
			url: '../ajax_process_form_submit.php',
			data: submitted_data,
			dataType: 'json',
			success: function (data) {
				displayMessage(data);
			},
			error: function (data) {
				displayMessage(data);
			}

		});

		return false;
	};


	var clearMessages = function () {
		var successMsgDiv = document.getElementById('successMsg');
		if (successMsgDiv != null) {
			successMsgDiv.parentNode.removeChild(successMsgDiv);
		}
		var errorMsgDiv = document.getElementById('errorMsg');
		if (errorMsgDiv != null) {
			errorMsgDiv.parentNode.removeChild(errorMsgDiv);
		}

	};

	var displayMessage = function (data) {
		clearMessages();
		var msgDivId;
		if (data.successful) {
			msgDivId = 'successMsg';
			form.hide();
		}
		else {
			msgDivId = 'errorMsg';
		}

		var msgDiv = document.createElement("div");
		msgDiv.setAttribute("id", msgDivId);
		var child = document.getElementById('mainblock');
		child.parentNode.insertBefore(msgDiv, child);
		msgDiv.innerHTML = data.msgText;

	};


});

