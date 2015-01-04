// - - - - - - - - - - - - - - - - - - - - -
//
// Title : Dynamic Resolution Dependent Layout Demo
// Author : Kevin Hale
// URL : http://particletree.com
//
// Description : This is a demonstration of a dynamic 
// resolution dependent layout in action. Change your browser 
// window size to see the layout respond to your changes. To 
// preserve the separation of the presentation and behavior 
// layers, this implementation delegates all the presentation 
// details to external CSS stylesheets instead of changing 
// each style property through JavaScript.
//
// Created : July 30, 2005
// Modified : November 15, 2005
//
// - - - - - - - - - - - - - - - - - - - - -

var lowEnd = 1100;
var hiEnd = 1600;

// getBrowserWidth is taken from The Man in Blue Resolution Dependent Layout Script
// http://www.themaninblue.com/experiment/ResolutionLayout/
function getBrowserWidth(){
    if (window.innerWidth){
        return window.innerWidth;}
    else if (document.documentElement && document.documentElement.clientWidth != 0){
        return document.documentElement.clientWidth;	}
    else if (document.body){return document.body.clientWidth;}
    return 0;
}

// dynamicLayout by Kevin Hale
function dynamicLayout(){
    var browserWidth = getBrowserWidth();

    //Load Thin CSS Rules
    if (browserWidth < lowEnd){
        changeLayout("narrow");
    }
    //Load Wide CSS Rules
    if ((browserWidth >= lowEnd) && (browserWidth <= hiEnd)){
        changeLayout("normal");
    }
    //Load Wider CSS Rules
    if (browserWidth > hiEnd){
        changeLayout("wide");
    }
}

// changeLayout is based on setActiveStyleSheet function by Paul Sowdon 
// http://www.alistapart.com/articles/alternate/
function changeLayout(description){
    var i, a;
    for(i=0; (a = document.getElementsByTagName("link")[i]); i++){
        if(a.getAttribute("title") !== null){
            a.disabled = true;
        }
        if(a.getAttribute("title") == description){
            a.disabled = false;
        }
    }
}

//addEvent() by John Resig
function addEvent( obj, type, fn ){
    if (obj.addEventListener){
        obj.addEventListener( type, fn, false );
    }
    else if (obj.attachEvent){
        obj["e"+type+fn] = fn;
        obj[type+fn] = function(){ obj["e"+type+fn]( window.event ); };
        obj.attachEvent( "on"+type, obj[type+fn] );
    }
}

function freshStyle(stylesheet){
    $('#mainStyle').attr('href',stylesheet);
}

//Run dynamicLayout function when page loads and when it resizes.
addEvent(window, 'load', dynamicLayout);
addEvent(window, 'resize', dynamicLayout);