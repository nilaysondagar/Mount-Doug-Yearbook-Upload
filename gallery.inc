<!--
	Filename: gallery.inc
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
-->

<form action="formprocess.php?page=gallery" method="post">

 	<input type="text" id="search" name="search" value="<?php if(isset($_POST['search'])) echo $_POST['search'];?>" placeholder="Search...">
	<input type="submit" name="page" value="Go" id="go">

	<select name="sort" id="select:" onchange="this.form.submit()">
 		<option value="sdate" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sdate')?'selected="selected"':''; ?> >Upload Date</option>
  		<option value="sfirst" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sfirst')?'selected="selected"':''; ?> >First Name</option>
  		<option value="slast" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'slast')?'selected="selected"':''; ?> >Last Name</option>
	</select>

</form>

<br>

<h2>Public Gallery</h2>

<?php
	if($isEditor == true) {
		echo '<div id="downloadAll" onClick="' . "downloadAll('gallery')" . '">Download All</div>';
		echo '<img id="dloadButton" alt="downloadButton" src="downloadbutton.png" onClick="download()">';
	}//if
?> 

<div id="close" onClick="hideLightBox()"><br>X</div><br>
<div id="fade" onClick="hideLightBox()"></div> 

<img src="previousbutton.png" alt='previousImage' id="previous" onClick="previousImage('gallery')"/>

<div id="lightbox">
	<div id="imagebox">	
		<img id="lightboximage" alt='lightboxImage' src="1x1.png"/>
		<p id="name"></p>
		<p id="desc"></p>
	</div>
</div>

<img src="nextbutton.png" alt='nextImage' id="next" onClick="nextImage('gallery')"/>