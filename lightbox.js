/*
	Filename: lightbox.js
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
*/

// global variables
var bigImage;
var name;
var desc;
var tags;
var index;

// show lightbox
function showLightBox($image, $name, $desc) {
	
	//assign source variables
	bigImage = $image;
	name = $name;
	desc = $desc.replace("#039", "'");
	temp = bigImage.split("/");
	temp2 = temp[1].split(".");
	index = temp2[0];
	console.log(desc);
	var addName = document.createTextNode(name);
	var addDesc = document.createTextNode(desc);
	
	// assign lightbox image
	document.getElementById("lightboximage").src = bigImage;
	
	//add name and description below photo
	document.getElementById("name").innerHTML = "<strong>Photo By: </strong>";
	document.getElementById("name").appendChild(addName);
	document.getElementById("desc").innerHTML = "<strong>Description: </strong>";
	document.getElementById("desc").appendChild(addDesc);

	// show all lightbox elements
	$("#editButton".concat(index)).fadeIn();
	$("#lightbox").fadeIn();
	$("#fade").fadeIn();
	$("#close").fadeIn();
	$("#previous").fadeIn();
	$("#next").fadeIn();
	$("#dloadButton").fadeIn();

} // showLightBox

// hide light box
function hideLightBox() {
	
	// variables
	var buttons = document.getElementsByClassName("hideEditButton");
	var edits = document.getElementsByClassName("hideEdit");

	// hide all lightbox elementss
	$("#editButton".concat(index)).fadeOut();
	$("#lightbox").fadeOut();
	$("#fade").fadeOut();
	$("#close").fadeOut();
	$("#previous").fadeOut();
	$("#next").fadeOut();
	$("#dloadButton").fadeOut();

	// hide all buttons and edit views
	for(var i = 0; i < buttons.length; i++) {
		buttons[i].style.display = "none";
		edits[i].style.display = "none";
	} // for

} // hideLightBox

function showEditPic($index) {

	// get php variable
	index = $index;

	// show or hide edit view
	if(document.getElementById("editPic".concat(index)).style.display == "block") {
		$("#editPic".concat(index)).slideUp();
	} else {
		$("#editPic".concat(index)).slideDown();
	} // else if

} // showEditPic

// display next image in lightbox view
function nextImage(page) {
	
	//cycle through images until match is found
	for(var i = 0; i < imageArray.length; i++) {
		
		//if photo is found, go to next image
		if(imageArray[i] == bigImage) {
			
			// if last image, display first image
			if(i == (imageArray.length - 1)) {

				// assign lightbox variables
				bigImage = imageArray[0];
				name = nameArray[0];
				desc = descArray[0];

				// hide edit buttons
				if(page == 'all') {
					document.getElementById("editButton".concat(index)).style.display = "none";
					$("#editPic".concat(index)).slideUp();
				} // if

			} else {

				// assign lightbox variables
				bigImage = imageArray[i+1];
				name = nameArray[i+1];
				desc = descArray[i+1];

				//hide edit buttons
				if(page == 'all') {
					document.getElementById("editButton".concat(index)).style.display = "none";
					$("#editPic".concat(index)).slideUp();
				} // if

			} // if else

			// show next image in lightbox
			showLightBox(bigImage, name, desc);
			break;

		} // if

	} // for

} // nextImage

//display previous image in lightbox view
function previousImage(page) {

	//cycle through images until match is found
	for(var i = 0; i < imageArray.length; i++) {

		//if photo is found, go to previous image
		if(imageArray[i] == bigImage) {
			
			// if first image, display last image
			if(i == 0) {

				// assign lightbox variables
				bigImage = imageArray[(imageArray.length - 1)];
				name = nameArray[(imageArray.length - 1)];
				desc = descArray[(imageArray.length - 1)];

				// hide edit buttons
				if(page == 'all') {
					document.getElementById("editButton".concat(index)).style.display = "none";
					$("#editPic".concat(index)).slideUp();
				} // if

			} else {

				// assign lightbox variables
				bigImage = imageArray[i-1];
				name = nameArray[i-1];
				desc = descArray[i-1];

				// hide edit buttons
				if(page == 'all') {
					document.getElementById("editButton".concat(index)).style.display = "none";
					$("#editPic".concat(index)).slideUp();
				} // if

			} // if else
		
			// show previous image in lightbox
			showLightBox(bigImage, name, desc);
			break;

		} // if

	} // for

} // previousImage

//download photo from lightbox view
function download() {
	a = document.createElement('a'); 
	a.download = "download"; 
	a.href = bigImage; 
	a.click(); 
} // download

// download all images on page in .zip format
function downloadAll(page) {
	a = document.createElement('a');

	// name file based on current page
	if(page == 'all') { 
		a.download = "allphotos.zip"; 
		a.href = "allphotos.zip"; 
	} else if(page == 'gallery') {
		a.download = "publicphotos.zip"; 
		a.href = "publicphotos.zip"; 
	} else {
		a.download = "unapprovedphotos.zip"; 
		a.href = "unapprovedphotos.zip"; 
	}//if else

	a.click();
} // downloadAll
