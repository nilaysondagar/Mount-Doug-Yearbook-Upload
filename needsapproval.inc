<!--
	Filename: needsapproval.inc
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
-->

<form action="formprocess.php?page=waiting" method="post">
 	<!--<input type="checkbox" name="access2" value="private" class="radioaccess" onchange="this.form.submit()" <?php if(isset($_POST['access2'])) echo "checked='checked'"; ?>> Private -->
 	<input type="text" id="search" name="search2" value="<?php if(isset($_POST['search2'])) echo $_POST['search2'];?>" placeholder="Search...">
	<input type="submit" name="page2" value="Go" id="go">

	<select name="sort" id="select:" onchange="this.form.submit()">
 		<option value="sdate" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sdate')?'selected="selected"':''; ?> >Upload Date</option>
  		<option value="sfirst" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'sfirst')?'selected="selected"':''; ?> >First Name</option>
  		<option value="slast" <?php echo (isset($_POST['sort']) && $_POST['sort'] == 'slast')?'selected="selected"':''; ?> >Last Name</option>
	</select>
	
</form>

<br>

<h2>Unapproved Images</h2> 

<img id="dloadButton" alt='downloadButton' src="downloadbutton.png" onClick="download()">

<div id="close" onClick="hideLightBox()"><br>X</div><br>
<div id="fade" onClick="hideLightBox()"></div> 

<img src="previousbutton.png" alt='previousButton' id="previous" onClick="previousImage('waiting')"/>

<div id="lightbox">
	<div id="imagebox">	
		<img id="lightboximage" class='unapproved' alt='lightboxImage' src="1x1.png"/>
		<p id="name"></p>
		<p id="desc"></p>
	</div>
</div>

<img src="nextbutton.png" alt='nextButton' id="next" onClick="nextImage('waiting')"/>