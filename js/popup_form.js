var form;


// This is bad. Why?
//function openForm(formContainerId) {
var openForm = function(formContainerId) {
	form = new FormPopup(formContainerId);
	form.open();
//	alert("openForm")
	return false;
};

//function FormPopup(formContainerId) {
//the following is visible at both levels
var FormPopup = function(formContainerId) {
	var lightbox = document.getElementById(formContainerId),
		dimmer = document.createElement("div"),
		htmlForm = lightbox.getElementsByTagName("form")[0],
		width = 400,
		height = getHiddenDivHeight(formContainerId),
		infoboxOpacity;
		dimmer.className = 'dimmer';
		
	var show = function()
	{
		infoboxOpacity = document.getElementsByClassName("infobox")[0].style.opacity;
		document.getElementsByClassName("infobox")[0].style.opacity = "1";
		document.body.appendChild(dimmer);
		lightbox.style.visibility = 'visible';
		lightbox.style.display = 'block';
	};

	var hide = function()
	{
		document.body.removeChild(dimmer);
		lightbox.style.visibility = 'hidden';
		lightbox.style.display = 'none';
		document.getElementsByClassName("infobox")[0].style.opacity = infoboxOpacity;
	};

	dimmer.onclick = function(){
		console.log("dimmer clicked");
		hide();
	};

	window.onresize = function()
	{
		updateLightboxDimensions();
		updateDimmerDimensions();
	};

	var updateLightboxDimensions = function() {
		//lightbox.style.height = lightbox.getElementsByTagName("form")[0].style.height;
		var top = (window.innerHeight - height) / 4;
		var left = (window.innerWidth - width) / 2;
		lightbox.style.top = top + 'px';
		lightbox.style.left = left + 'px';
	};
	
	var updateDimmerDimensions = function() {
		dimmer.style.width =  window.innerWidth + 'px';
		dimmer.style.height = window.innerHeight + 'px';
	}

	function getHiddenDivHeight(divId)
	{
		var previousCss  = $("#"+divId).attr("style");

		$("#"+divId)
			.css({
				position:   'absolute', // Optional if #myDiv is already absolute
				visibility: 'visible',
				display:    'block',
				border:		'1px solid black'
			});

		var origFormHeight = $("#"+divId).height();
		$("#"+divId).attr("style", previousCss ? previousCss : "");


		return origFormHeight;
	}

	this.open = function()
	{
		show();
		updateLightboxDimensions();
		updateDimmerDimensions();


		var input_array = htmlForm.getElementsByTagName("input");
		for(var i=0; i<input_array.length; i++)
		{
			if(input_array[i].type == "text" || input_array[i].type == "password")
			{
				input_array[i].focus();
				break;
			}

		}

	};

	this.hide = function()
	{
		hide();
	};


};

/*
var submitForm = function () {
		alert("form submitted");
	var currentButton = document.getElementById("mySubmit");
		alert("currentButton = " + currentButton);
	//var action_id = currentButton.parent().parent().attr('id'); //this is div id, e.g.: "changePassword"
	var parent2 = currentButton.parent;
		alert("parent var = " + parent2); //undefined
	//var parent1 = currentButton.parent(); //fails
		//alert("parent func = " + parent1); //not reached
	var parent = currentButton.parentNode.nodeName;
		alert("parent no jquery = " + parent);

	var action_id = currentButton.parent().parent.attr('id');
		alert("action_id = " + action_id);
	var form_inputs = currentButton.siblings(); //array of inputs
		alert("inputs = " + form_inputs);
	var submitted_data = {action_id: action_id};
	alert("starting to populate data");
	for (var i = 0; i < form_inputs.length; i++) {
		var key = form_inputs[i].name;
		var value = form_inputs[i].value;
		if (typeof(key) != "undefined") {
			submitted_data[key] = value;
		}
	}
	alert("data populated");

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
	alert($.ajax);
	return false;
};
*/

$(function() {
	//This works only inside top level function
	$("div#account_links a.openForm").click(function () {
		var currentLink = $(this);
		var formContainerId = currentLink.attr('id');
		var tmp = formContainerId.split("_");
		form = new FormPopup(tmp[0]);
		form.open();
		return false;
	});
});