<!--
	Filename: content.inc
	Authors: Ethan Li and Nilay Sondagar
	Date: June 14th, 2016
	Purpose: A photo upload site for yearbook staff at MD
-->

<br>
<h2>Grad Baby Photo Uploader</h2>

<form action="formprocess.php" id="imageform" method="post" enctype="multipart/form-data">
	
	First Name<span class="error"> <?php echo $fname_err;?></span>
    <br><input type="text" required name="fname" value="<?php echo $fname;?>"><br><br>
	
	Last Name<span class="error"> <?php echo $lname_err;?></span>
	<br><input type="text" required name="lname" value="<?php echo $lname;?>"><br><br>
	
	File:<span class="error"> <?php echo $file_err;?></span>
	<input type="file" name="file" id='fileupload' required><br><br>
	
	Description<span class="error"> <?php echo $desc_err;?></span>
	<br><textarea name="desc" required><?php echo $desc;?></textarea><br>
	
	Tags<span class="error"> <?php echo $tags_err;?></span>
	<br><textarea name="tags" placeholder="Separate tags using a comma" required><?php echo $tags;?></textarea><br>
	
	<input type="checkbox" required name="copyright" value="Copyright" <?php if(isset($_POST['copyright'])) echo "checked='checked'"; ?>> I retain the copyright to this image and release it for use on this site.<br><br>
	
	<input type="radio" required name="access" value="public" <?php if(!isset($_POST['privacy']) || (isset($_POST['privacy']) && $_POST['privacy'] == 'public')) echo ' checked="checked"'?>>Public<br>
	<input type="radio" name="access" value="private" <?php if((isset($_POST['privacy']) && $_POST['privacy'] == 'private')) echo ' checked="checked"'?>>Private<br><br>
	
	<input type="submit" name="submit" value="Submit" id="submit">
	<button type="reset" value="Reset">Reset</button>
	
</form>