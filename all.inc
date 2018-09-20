<!--
	Filename: all.inc
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
-->

<?php
	if($content == "all") {
		echo '<form action="formprocess.php?page=all" method="post">';
	} else {
		echo '<form action="formprocess.php?page=editAll" method="post">';
	}//if else
?>

<!--<script>
	window.addEventListener("beforeunload", function(event) {

		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		 	xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
			xmlhttp.onreadystatechange=function()
			{
			  	if (xmlhttp.readyState==4 && xmlhttp.status==200)
		  		{
					alert('done')
		 		}
			}

		xmlhttp.open("POST","changestatus.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("name=allow");

    	event.returnValue = "Are you sure you want to leave?";
	});


</script> -->

 	<input type="text" id="search" name="search3" value="<?php if(isset($_POST['search3'])) echo $_POST['search3'];?>" placeholder="Search...">
	<input type="submit" name="page3" value="Go" id="go">

	<select name="sort" id="select:" onchange="this.form.submit()">
 		<option value="sdate" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sdate')?'selected="selected"':''; ?> >Upload Date</option>
  		<option value="sfirst" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sfirst')?'selected="selected"':''; ?> >First Name</option>
  		<option value="slast" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'slast')?'selected="selected"':''; ?> >Last Name</option>
	</select>

</form>

<br>

<h2>All Images</h2> 

<div id="downloadAll" onClick="downloadAll('all')">Download All</div>
<img id="dloadButton" alt='downloadButton' src="downloadbutton.png" onClick="download()">
<a id="editAll" onclick="return restrictpage();" href="?page=editAll">Edit All</a>

<div id="close" onClick="hideLightBox()"><br>X</div><br>
<div id="fade" onClick="hideLightBox()"></div> 

<?php
	if($content == 'all') {
		echo '<img src="previousbutton.png" alt=\'previousImage\' id="previous" onClick="previousImage(\'all\')"/>';
	} else {
		echo '<img src="previousbutton.png" alt=\'previousImage\' id="previous" onClick="previousImage(\'editAll\')"/>';
	}//if else
?>

<div id="lightbox">
	<div id="imagebox">	
		<img id="lightboximage" alt='lightboxImage' src="1x1.png"/>
		<p id="name"></p>
		<p id="desc"></p>
	</div>
</div>

<?php 
	if($content == 'all') {
		echo '<img src="nextbutton.png" alt=\'nextImage\' id="next" onClick="nextImage(\'all\')"/>';
	} else {
		echo '<img src="nextbutton.png" alt=\'nextImage\' id="next" onClick="nextImage(\'editAll\')"/>';
	}//if else
?>