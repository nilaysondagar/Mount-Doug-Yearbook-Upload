<?php
session_start();

/*
	Filename: formprocess.php
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
*/

//variables
//$_SESSION["isAdmin"] = true;                                                   // set userview
$isEditor = $_SESSION["isEditor"];                                              // local variable for userview
$isAdmin = $_SESSION["isAdmin"];

$content = '';                                                                  // stores current page
$fname_err = $lname_err = $tags_err = $desc_err = $file_err = $access_err = ""; // error messages used in form
$fname = $lname = $tags = $file = $desc = $access = "";                         // stores form values
$form_valid = true;                                                             // stores whether form is valid or not
$my_file = "galleryinfo.json";                                                  // JSON File location
$names = array();
$json_array = array();                                                          // array for JSON file contents
$json_data = file_get_contents($my_file);                                       // string with JSON file contents
$form_array = array();                                                          // array to store form values
$dir = 'uploadedimages/';                                                       // directory for uploaded images
$image_array = array();                                                         // array for images on page
$name_array = array();                                                          // array for names on page
$desc_array = array();                                                          // array for descriptions on page
$upload_ok = 1;                                                                 // stores whether file should be uploaded or not
$sort_by = "";																	// stores sort by value
$edit_valid = true;																// stores whether edit is valid or not
$edit_err = array();															// stores editing errors
$page_status = "allow";															// stores edit status to prevent multiple edits at once

// include HTML header
include "header.inc";

// change page based off of links pressed
if(isset($_GET["page"])) {
	$content = $_GET["page"];
	$page_status = file_get_contents("editstatus.txt");
} // if

// change sort method
if($content == "gallery" && isset($_POST['sort']) || $content == "waiting" && isset($_POST['sort']) || $content == "editAll" && isset($_POST['sort']) || $content == "all" && isset($_POST['sort'])) {
    $sort_by = $_POST['sort'];
} // if

// validate the edits made
if(isset($_POST['page5']) && $_POST['page5'] == 'Complete' || isset($_POST['page4']) && $_POST['page4'] == 'Complete') {

	// get contents from .json and convert to array
	$json_data = file_get_contents($my_file);
	$json_array = json_decode($json_data, true);


	// find final index of JSON array
	end($json_array);
	$last_index = key($json_array); 

	// loop through array to change properties
    for($i = 0; $i <= $last_index; $i++) { 

    	//error check values if the values exist
		if(isset($_POST['modname' . $i]) || isset($_POST['moddesc' . $i]) || isset($_POST['modtags' . $i])) {

			//check for empty fields
			if(empty($_POST['modname' . $i]) && empty($_POST['moddelete' . $i]) || empty($_POST['moddesc' . $i]) && empty($_POST['moddelete' . $i]) || empty($_POST['modtags' . $i]) && empty($_POST['moddelete' . $i])) {
				$edit_valid = false;
				array_push($edit_err, "All fields must be filled out.");
			} // if
        
        	// error check first and last name
			error_check_array($_POST['modname' . $i], "/^[a-zA-Z\- ]*$/", $edit_err, '"' . $_POST['modname' . $i] . '"' . " is an invalid name. Only letters and whitespace is allowed.", $_POST['modname' . $i], $edit_valid);

			// error check description
			$_POST['moddesc' . $i] = str_replace("\\", "", $_POST['moddesc' . $i]);
			error_check_array($_POST['moddesc' . $i], "/^[a-zA-Z0-9\-!@#$%^&*()_=',+?\"., ]*$/", $edit_err, '"' . $_POST['moddesc' . $i] . '"' . " is an invalid description. Only letters, numbers, whitespace and !@#$%^&*()_-=',+?\"., are allowed.", $_POST['moddesc' . $i], $edit_valid);
			$_POST['moddesc' . $i] = addslashes(test_input($_POST['moddesc' . $i]));

			// error check tags
			error_check_array($_POST['modtags' . $i], "/^[a-zA-Z0-9, ]*$/", $edit_err, '"' . $_POST['modtags' . $i] . '"' . " are invalid tags. Only letters, numbers, whitespace and commas are allowed.", $_POST['modtags' . $i], $edit_valid);

		} // if

	} // for

		$content = 'editAll';
	
} // if

// change name, description and tags of all images
if(isset($_POST["page5"]) && $_POST["page5"] == "Complete" && $edit_valid == true || isset($_POST["page4"]) && $_POST["page4"] == "Complete" && $edit_valid == true) {

	// get contents from .json and convert to array
	$json_data = file_get_contents($my_file);
	$json_array = json_decode($json_data, true);

	// find final index of JSON array
	end($json_array);
	$last_index = key($json_array); 

	// loop through array to change properties
	for($i = 0; $i <= $last_index; $i++) { 

		// change values if they are set
		if(isset($_POST['modname' . $i]) && isset($_POST['moddesc' . $i]) && isset($_POST['modtags' . $i])) {

			// split name field into first and last name
			$names = explode(" ", $_POST['modname' . $i]);

			// set first name in array
            $json_array[$i]["firstname"] = ucfirst(strtolower($names[0]));

            // if a last name is set, set last name
			if(isset($names[1])) {

				// set last name
				$json_array[$i]["lastname"] = ucfirst(strtolower($names[1]));

				// add to last name
				for($j = 2; $j < count($names); $j++) {
					$json_array[$i]["lastname"] .= " " . ucfirst(strtolower($names[$j]));
				}// for

			} else {
				$json_array[$i]["lastname"] = "";
			} // if else

			// set other values in array
			$json_array[$i]["description"] = $_POST['moddesc' . $i];
			$json_array[$i]["tags"] = $_POST['modtags' . $i];

		} // if

		if(isset($_POST['moddelete' . $i])) {
			$for_delete = $dir . $i;
            $json_array = delete($for_delete, $dir, $i, $json_array);
		} // if

	} // for

	// overwrite .json file with all values
	$json_data = json_encode($json_array, JSON_PRETTY_PRINT);
	file_put_contents($my_file, $json_data);

	// change page to All Images
	$content = 'all';
} // if

// delete or approve images based on checkboxes ticked
if(isset($_POST["page2"]) && $_POST["page2"] == "Complete") {

	// get contents from .json and convert to array
	$json_data = file_get_contents($my_file);
	$json_array = json_decode($json_data, true);

	// find final index of JSON array
	end($json_array);
	$last_index = key($json_array); 

	// loop through array to delete or approve images
	for($i = 0; $i <= $last_index; $i++) { 

		// make sure user does not try to approve and delete an image
		if(isset($_POST['modapprove' . $i]) && isset($_POST['moddelete' . $i])) {
			echo "<script>";
			echo "alert('You cannot approve AND delete an image!');";
			echo "</script>";

		// approve images if checkbox is ticked
		} else if(isset($_POST['modapprove' . $i])) {
			$json_array[$i]["approval"] = "true";

		// delete images if checkbox is ticked
		} else if(isset($_POST['moddelete' . $i])) {
			$for_delete = $dir . $i;
		    $json_array = delete($for_delete, $dir, $i, $json_array);
		} // if

	} // for

	// overwrite .json file with all values
	$json_data = json_encode($json_array, JSON_PRETTY_PRINT);
	file_put_contents($my_file, $json_data);
	
} // if

// form page error checking
if($content != "waiting" && $content != "gallery" && $content != "editAll" && $content != "all") {

	if(isset($_SESSION["name"])) {
			$names = explode(", ", $_SESSION["name"]);
			$fname = $names[1];
			$lname = $names[0];
	} // if

	// error checking
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		// error check first name
		list($fname_err, $fname) = empty_check($_POST['fname']);
		error_check($_POST['fname'], "/^[a-zA-Z\-]*$/", $fname_err, "Only letters allowed", $fname, $form_valid);

		// error check last name
		list($lname_err, $lname) = empty_check($_POST['lname']);
		error_check($_POST['lname'], "/^[a-zA-Z\-]*$/", $lname_err, "Only letters allowed", $lname, $form_valid);

		/*-------------------- FILE CHECKING --------------------*/

		// variables for file upload
		$target_file = $dir . basename($_FILES["file"]["name"]);
		$upload_ok = 1;
		$image_file_type = pathinfo($target_file,PATHINFO_EXTENSION);

		// check for empty file
		if (empty($_FILES["file"]) || $_FILES["file"] ["name"] === "") {
			$file_err = "File is required";
			$upload_ok = 0;
			$form_valid = false;	
		} // if

		// check for valid images
		if($upload_ok == 1) {
			$check = getimagesize($_FILES["file"]["tmp_name"]);
		} else {
			$check = false;
		} // if/else

		// check for valid image
		if($check !== false) {
			$upload_ok = 1;
		} else {
			$file_err = "File is not an image.";
			$upload_ok = 0;
			$form_valid = false;
		} // if/else

		// check for valid file size (less than 2MB)
		if ($_FILES["file"]["size"] > 2097152 && $upload_ok == 1) {
			$file_err = "Sorry, your file is too large.";
			$upload_ok = 0;
			$form_valid = false;
		} // if

		// check for valid image types (jpg, jpeg & png)
		if(strtolower($image_file_type) != "jpg" && strtolower($image_file_type) != "png" && strtolower($image_file_type) != "jpeg" && $upload_ok == 1) {
			$file_err = "Sorry, only JPG, JPEG, PNG files are allowed.";
			$upload_ok = 0;
			$form_valid = false;
		} // if

		/*-------------------- FORM CHECKING --------------------*/

		// error check descriptions
		list($desc_err, $desc) = empty_check($_POST['desc']);
		error_check($_POST['desc'], "/^[a-zA-Z0-9\-!@#$%^&*()_=',+?\"., ]*$/", $desc_err, "Only letters, numbers, whitespace and !@#$%^&*()_-=',+?\"., are allowed.", $desc, $form_valid);
		$_POST['desc'] = test_input($_POST['desc']);

		// error check tags
		list($tags_err, $tags) = empty_check($_POST['tags']);
		error_check($_POST['tags'], "/^[a-zA-Z0-9, ]*$/", $tags_err, "Only letters, numbers & commas allowed", $tags, $form_valid);

		// set access as public or private
		$access = test_input($_POST["access"]);

	} // if

} // if

// if submit is clicked and data is valid, show form completion page  
if(isset($_POST["submit"]) && $_POST["submit"] == "Submit" && $form_valid == true){

	// include "Successfully Submitted" page
    include "content2.inc";

	// check if $upload_ok is set to 0 by an error
    if ($upload_ok == 0) {
    	echo "Sorry, your file was not uploaded.";

	// if everything is valid, try to upload file
    } else {

		// varibles for file upload and naming
    	$counter = file_get_contents("counter.txt");
    	$temp = explode(".", $_FILES["file"]["name"]);
    	$new_file_name = $counter . '.' . end($temp);
    	$new_file_name = strtolower($new_file_name);
    	$create_thumb_name = 'uploadedimages/' . strtolower($new_file_name);

    	// upload file and create thumbnails
    	if(move_uploaded_file($_FILES["file"]["tmp_name"], "uploadedimages/" . $new_file_name)) {
    		echo "The file ". basename($_FILES["file"]["name"]). " has been uploaded.";
    		
			$image_file_type = pathinfo($new_file_name, PATHINFO_EXTENSION);

    		// make thumbnail if .jpg or .jpeg
			if($image_file_type == "jpg" || $image_file_type == "jpeg") {
				$im = imagecreatefromjpeg($create_thumb_name);

			// make thmbnail if .png
			} else {
				$im = imagecreatefrompng($create_thumb_name);
			} // if/else

			$ini_x_size = getimagesize($create_thumb_name)[0];
			$ini_y_size = getimagesize($create_thumb_name)[1];

			// the minimum of xlength and ylength to crop
			$crop_measure = min($ini_x_size, $ini_y_size);

			// crop images
			$to_crop_array = array('x' => 0 , 'y' => 0, 'width' => $crop_measure, 'height'=> $crop_measure);
			$thumb_im = imagecrop($im, $to_crop_array);		

			// create thumbnail at ~50% quality
			if($image_file_type == "jpg" || $image_file_type == "jpeg") {
				imagejpeg($thumb_im, "thumbs/" . $counter . ".jpg", 50);
			} else {
				imagepng($thumb_im, "thumbs/" . $counter . ".png", 4);
			} // if/else

    		$counter++;

    		// write new naming number to text file
    		file_put_contents("counter.txt", $counter);

    	//if there was an error uploading, show error message
    	} else {
    		echo "Sorry, there was an error uploading your file.";
    	} // if/else

    } // if/else

    // get contents from .json and convert to array
	$json_data = file_get_contents($my_file);
	$json_array = json_decode($json_data, true);

	// create array with new form values
	$form_array = array("firstname" => ucfirst(strtolower($fname)), "lastname"=> ucfirst(strtolower($lname)), "image"=> $file, "description"=> $desc, "tags"=> $tags, "access"=> $access, "permission"=>"true", "approval"=> "false");

	// if the .json file is empty, make it equal to the new form values
	if(empty($json_array)) {
		$json_array[0] = $form_array;

	// if not, add new form values to the end of the array
	} else {
	    $json_array[($counter - 1)] = $form_array;
	} // if/else

	// overwrite .json file with all values
	$json_data = json_encode($json_array, JSON_PRETTY_PRINT);
	file_put_contents($my_file, $json_data);

	// reset variables
    $fname = $lname = $tags = $file = $desc = $access = $file_err = "";

	// change page based off of links pressed
    if(isset($_GET["page"])) {
    	$content = $_GET["page"];
    } // if

    //show form page
    if($content == "form") {
    	include "content.inc";
    } // if

} else {

	// show form page
	if($content == "form") {
		include "content.inc";

	// show variable dump
	} else if($content == "dump" && $isEditor == true || $content == "dump" && $isAdmin == true) {
		echo "<br><h2>Variable Dump</h2>";
		echo "<pre>";
		var_dump(json_decode($json_data, true));
		echo "</pre>";

	// show needs approval page	
	} else if($content == "waiting" && $isEditor == true || isset($_POST['search2']) && $isEditor == true || isset($_POST['page2']) && $isEditor == true || $content == "waiting" && $isAdmin == true || isset($_POST['search2']) && $isAdmin == true || isset($_POST['page2']) && $isAdmin == true) {
		include "needsapproval.inc";

		// set all images in an array
		$json_array = get_images();

		// display form button at the top of page
		echo "<form action='formprocess.php?page=waiting' method='post'>\n";
		echo '<div id="downloadAll" onClick="downloadAll(\'waiting\')">Download All</div>';
		echo "<input type='submit' name='page2' value='Complete' id='complete2'><br>\n";
		echo "<ul>\n";

		// loop through array to find valid images to display
		foreach($json_array as $image) {

			// variables
			$image_file_type = pathinfo($image['image'], PATHINFO_EXTENSION);
			$temp = explode("/", $image['image']);
			$num = explode(".", $temp[1]);
			$index = $num[0]; 

			// set info from JSON file to appropriate image
			$name = $json_array[$index]["firstname"] . " " . $json_array[$index]["lastname"];
			$desc = $json_array[$index]["description"];
			$access = $json_array[$index]["access"];
			$tags = strtolower($json_array[$index]["tags"]);
			$approval = $json_array[$index]["approval"];
			
			// validate search term for tags
			if(isset($_POST['search2']) && $_POST['search2'] !== "") {
			    $searchfor = strtolower($_POST['search2']);

				if(strpos($tags, $searchfor) !== false) {
					$valid = true;
				} else {
					$valid = false;
				} // if/else

			} else {
				$valid = true;
			} // if/else

			// print out images if valid
			if($valid == true && $approval == "false" || $valid == true && $approval == "false") {

				// alter description for displaying info in HTML file
				$desc = str_replace('&quot;', '\"', $desc);

				// print out images in HTML
				echo "<li class='needsApproval'>";
				
				if($image_file_type == "jpg" || $image_file_type == "jpeg") {
					echo "<img class='image' alt='image' src='thumbs/" . $index . ".jpg' onClick='showLightBox("; 
				} else {
					echo "<img class='image' alt='image' src='thumbs/" . $index . ".png' onClick='showLightBox("; 
				} // if/else

				echo '"' . $image['image'] . '", ' . '"' . $name . '", ' . '"' . $desc;
				echo '")' . "'/>\n";	
				echo "<br><input type='checkbox' name='modapprove" . $index . "' value='true'> Approve\n";
				echo "<br><input type='checkbox' name='moddelete" . $index . "' value='true'> Delete\n";
				echo "</li>\n";

				// add new info to arrays
				array_push($image_array, $image['image']);
				array_push($name_array, $name);
				$desc = stripslashes(html_entity_decode($desc, ENT_QUOTES));
				array_push($desc_array, $desc);

			} // if

		} // foreach	

		echo "</ul>";
		echo "</form>\n";

		// pass info to JavaScript file
		echo '<script>';
		echo 'var imageArray = ' . json_encode($image_array) . ';';
		echo 'var nameArray = ' . json_encode($name_array) . ';';
		echo 'var descArray = ' . json_encode($desc_array) . ';';
		echo '</script>';

		// create .zip file of displayed images
		$result = create_zip($image_array,'unapprovedphotos.zip');

	//show public gallery page
	} else if($content == "gallery" || isset($_POST['search']) && $content == 'gallery') {
		include "gallery.inc";

		// set all images in an array
		$json_array = get_images();

		echo "<ul>\n";
		
		foreach($json_array as $image) {
			
			// variables
			$image_file_type = pathinfo($image["image"], PATHINFO_EXTENSION);
			$temp = explode("/", $image["image"]);
			$num = explode(".", $temp[1]);
			$index = $num[0];

			// set info from JSON file to appropriate image
			$name = $json_array[$index]["firstname"] . " " . $json_array[$index]["lastname"];
			$desc = $json_array[$index]["description"];
			$access = $json_array[$index]["access"];
			$tags = strtolower($json_array[$index]["tags"]);
			$approval = $json_array[$index]["approval"];

			if(isset($_POST['search']) && $_POST['search'] !== "") {
			    $searchfor = strtolower($_POST['search']);

				if(strpos($tags, $searchfor) !== false) {
					$valid = true;
				} else {
					$valid = false;
				} // if/else					

			} else {
				$valid = true;
			} // if/else

			// print out image if valid
			if($access == "public" && $valid == true && $approval == "true") {

				$desc = str_replace('&quot;', '\"', $desc);

			    // print out image in HTML
		    	echo "<li class='container'>";

			    if($image_file_type == "jpg" || $image_file_type == "jpeg") {
				    echo "<img class='image' alt='image' src='thumbs/" . $index . ".jpg' onClick='showLightBox("; 
			    } else {
			    	echo "<img class='image' alt='image' src='thumbs/" . $index . ".png' onClick='showLightBox(";
			    } // if

			    echo '"' . $image["image"] . '", ' . '"' . $name . '", ' . '"' . $desc;
			    echo '")' . "'/>\n";
			    echo "</li>\n";	

			    // add new info to array
			    array_push($image_array, $image["image"]);
			    array_push($name_array, $name);
			    $desc = stripslashes(html_entity_decode($desc, ENT_QUOTES));
			    array_push($desc_array, $desc);

			} // if

		} // foreach	

		echo "</ul>";

		// pass info to JavaScript
		echo '<script>';
		echo 'var imageArray = ' . json_encode($image_array) . ';';
		echo 'var nameArray = ' . json_encode($name_array) . ';';
		echo 'var descArray = ' . json_encode($desc_array) . ';';
		echo '</script>';

		// create .zip file
		$result = create_zip($image_array,'publicphotos.zip');

	// show all images page
	} else if($content == "all" && $isEditor == true || $content == "editAll" && $isEditor == true || isset($_POST['search3']) && $isEditor == true || isset($_POST['page']) && $isEditor == true || $content == "all" && $isAdmin == true || $content == "editAll" && $isAdmin == true || isset($_POST['search3']) && $isAdmin == true || isset($_POST['page']) && $isAdmin == true) {
		include "all.inc";
		
		// set all images into an array
		$json_array = get_images();

		if($content == "editAll") {
			echo "<form action='formprocess.php?page=all' method='post'>\n";
			echo "<input type='submit' name='page5' value='Complete' id='complete'><br>\n";
			echo "<br>";
		} // if

		for ($i = 0; $i < count($edit_err); $i++) { 
			$edit_err[$i] = htmlspecialchars($edit_err[$i]);
			echo $edit_err[$i];
		} // for

		echo "<ul>\n";

		foreach($json_array as $image) {
			
			// variables
			$image_file_type = pathinfo($image["image"], PATHINFO_EXTENSION);
			$temp = explode("/", $image["image"]);
			$num = explode(".", $temp[1]);
			$index = $num[0];

			// set info from JSON file to appropriate image
			$name = $json_array[$index]["firstname"] . " " . $json_array[$index]["lastname"];
			$desc = $json_array[$index]["description"];
			$access = $json_array[$index]["access"];
			$tags = strtolower($json_array[$index]["tags"]);
			$approval = $json_array[$index]["approval"];

			if(isset($_POST['search3']) && $_POST['search3'] !== "") {
				$searchfor = strtolower($_POST['search3']);

		    	if(strpos($tags, $searchfor) !== false) {
						$valid = true;
				} else {
						$valid = false;
				} // if/else
	
			} else {
					$valid = true;
			} // if/else

			// print out image if valid
			if($access == "public" && $valid == true && $approval == "true"|| $access == "private" && $isEditor == "true" && $valid == true && $approval == "true" || $access == "private" && $isAdmin == "true" && $valid == true && $approval == "true") {

				$desc = str_replace('&quot;', '\"', $desc);

				// print out image in HTML
				echo "<li class='container'>";

				if($image_file_type == "jpg" || $image_file_type == "jpeg") {
					echo "<img class='image' alt='image' src='thumbs/" . $index . ".jpg' onClick='showLightBox("; 
				} else {
					echo "<img class='image' alt='image' src='thumbs/" . $index . ".png' onClick='showLightBox(";
				} // if/else

				echo '"' . $image["image"] . '", ' . '"' . $name . '", ' . '"' . $desc;
				echo '")' . "'>\n";

				// print out edit all input fields
				if($content == "editAll") {
					echo "<br><input type='text' class='editprop2' name='modname" . $index . "' value='";
					if(isset($_POST['modname' . $index])) {
						echo $_POST['modname' . $index];
					} else {
						echo $name;
					} // if/else
					echo "'>\n";
					echo "<br><input type='text' class='editprop2' name='moddesc" . $index . "' value='"; 
					if(isset($_POST['moddesc' . $index])) {
						echo $_POST['moddesc' . $index];
					} else {
						echo $desc;
					} // if/else
					echo "'>\n";
					echo "<br><input type='text' class='editprop2' name='modtags" . $index . "' value='";
					if(isset($_POST['modtags' . $index])) {
						echo $_POST['modtags' . $index];
					} else {
						echo $tags;
					} // if/else
					echo "'>\n";
					echo "<br><input type='checkbox' name='moddelete" . $index . "' value='true'> Delete\n";
				} // if

				echo "</li>\n";	

				// print out singular edit views
    			if($content != "editAll") {
					echo "<li><button id='editButton" . $index . "' class='hideEditButton' onClick='showEditPic(" . $index . ")'>Edit</button>\n";
					echo "<form action='formprocess.php?page=all' method='post' class='hideEdit' id='editPic" . $index . "'>\n";
					echo "<input type='text' class='editprop' name='modname" . $index . "' value='";
					if(isset($_POST['modname' . $index])) {
						echo $_POST['modname' . $index];
					} else {
						echo $name;
					} // if/else
					echo "'>\n";
					echo "<br><input type='text' class='editprop' name='moddesc" . $index . "' value='";
					if(isset($_POST['moddesc' . $index])) {
						echo $_POST['moddesc' . $index];
					} else {
						echo $desc;
					} // if/else
					echo "'>\n";
					echo "<br><input type='text' class='editprop' name='modtags" . $index . "' value='";
					if(isset($_POST['modtags' . $index])) {
						echo $_POST['modtags' . $index];
					} else {
						echo $tags;
					} // if/else
					echo "'>\n";
					echo "<br><input type='checkbox' name='moddelete" . $index . "' value='true'> Delete\n";
					echo "<br><br><input type='submit' name='page4' value='Complete' class='completeEdit'>\n";
					echo "</form></li>";
				} // if

				$desc = stripslashes(html_entity_decode($desc, ENT_QUOTES));

				// add new info to array
				array_push($image_array, $image["image"]);
				array_push($name_array, $name);
				array_push($desc_array, $desc);

			} // if

		} // foreach	

		echo "</ul>";

		if($content == 'editAll') {
			file_put_contents("editstatus.txt", "deny");
			echo "</form>\n";	
		} // if

		// pass info to JavaScript
		echo '<script>';
		echo 'var imageArray = ' . json_encode($image_array) . ';';
		echo 'var nameArray = ' . json_encode($name_array) . ';';
		echo 'var descArray = ' . json_encode($desc_array) . ';';
		echo '</script>';

		// create .zip file
		$result = create_zip($image_array,'allphotos.zip');
	} else {
		include "content.inc";
	} // if/else

} // if/else

// strip unnecessary characters 
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data, ENT_QUOTES);

	return $data;
	
} // test_input

// create .zip files
function create_zip($files = array(), $destination = '', $overwrite = false) {

	// if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { 
		return false; 
	} // if

	// vars
	$valid_files = array();

	// if files were passed in
	if(is_array($files)) {

		// cycle through each file
		foreach($files as $file) {
		
			// make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			} // if
			
		} // foreach

	} // if

	// if we have good files...
	if(count($valid_files)) {

		// create the archive
		$zip = new ZipArchive();

		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		} // if

		// add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		} // foreach

		// close the zip
		$zip->close();

		// check to make sure the file exists
		return file_exists($destination);
	} else {
		return false;
	} // if/else

} // create_zip

//sort photos by last name
function sort_by_last($a, $b) {

    if ($a['lastname'] == $b['lastname']) {
        return 0;
    } // if
	
    return ($a['lastname'] < $b['lastname']) ? -1 : 1;
	
} // sort_by_last

// if file exists, delete image from server and remove info from JSON file
function delete($img, $dir, $index, $array) {
	
	if(file_exists($img . ".jpeg")) {
 	    unlink($dir . $index . ".jpeg");
 	    unlink('thumbs/' . $index . ".jpg");
    	unset($array[$index]);
	} else if(file_exists($img . ".jpg")) {
  		unlink($dir . $index . ".jpg");
  		unlink('thumbs/' . $index . ".jpg");
    	unset($array[$index]);
	} else if(file_exists($img . ".png")) {
    	unlink($dir . $index . ".png");
    	unlink('thumbs/' . $index . ".png");
    	unset($array[$index]);
	} else {
		echo "File does not exist";
	} // if/else
			
	return $array; 
			
} // delete

// check if field is empty
function empty_check($value) {

	global $form_valid;

	if(empty($value)) {
		$array[0] = "This field is required";
		$array[1] = "";
		$form_valid = false;
	} else {
		$array[1] = test_input($value);
		$array[0] = "";
	} // if/else

	return $array;

} // error_check

// validate user input
function error_check($value, $param, &$error, $error_msg, &$name, &$valid) {
	
	// check for invalid characters in first name
	if (!preg_match($param, $value)) {
		$error = $error_msg;
		$name = "";
		$valid = false;
	} // if

} // error_check

// validate user input and store errors in an array
function error_check_array($value, $param, &$error, $error_msg, &$name, &$valid) {
	
	// check for invalid characters in first name
	if (!preg_match($param, $value)) {
		array_push($error, $error_msg);
		$name = "";
		$valid = false;
	} // if

} // error_check

// get all images and put it into an array
function get_images() {
	global $sort_by, $dir, $my_file;

	// find images with appropriate extension
	$images = glob($dir."*."."{png,jpg,jpeg}", GLOB_BRACE);

	// sort the images array
	natsort($images);

	// read .json file
	$json_data = file_get_contents($my_file);
	$json_array = json_decode($json_data, true);

	// set .json file image fields
	foreach($images as $image) {
		$temp = explode("/", $image);
		$num = explode(".", $temp[1]);
		$index = $num[0];
		$json_array[$index]['image'] = $image;
	} // foreach

	// sort images
	if($sort_by == "sfirst") {
		asort($json_array);
	} else if($sort_by == "slast") {
		uasort($json_array, 'sort_by_last');
	} // if

	return $json_array;

} // get_images

// include footer of HTML code
include "footer.inc";

?>