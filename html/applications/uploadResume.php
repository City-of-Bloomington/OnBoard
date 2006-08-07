<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); 
			# User only brought to this page if there was an upload error
			
			# Error message handling for uploads
			$error = "";
			if(isset($_GET['err']))
			{
				switch($_GET['err']) 
				{
					case 0:
					$error = "";
					break;
					case 1:
					$error = "The uploaded file exceeds the maximum file size.";
					break;
					case 2:
					$error = "The uploaded file exceeds the maximum file size.";
					break;
					case 3:
					$error = "The uploaded file was only partially uploaded.";
					break;
					case 4:
					$error = "No file was uploaded.";
					break;
					case 5:
					$error = "Incorrect file type. Please submit a PDF or MS Word Document.";
					break;
					case 6:
					$error = "Missing a temporary folder.";
					break;
					case 7:
					$error = "Failed to write file to disk.";
					break;
				}
			}
	
	?>
	
	<h2>Board and Commission Application | Upload Resume</h2>
	<p>Your application was submitted successfully but there was a problem with your upload.</p>
		<form enctype="multipart/form-data" method="post" action="application.php?id=<?php echo $_GET['id']; ?>">
   			<h4><?php echo $error; ?></h4>
  	  		<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
   			<table>	
   				<tr><td><label for="userfile">Upload Resume</label></td>
    				<td><input id="userfile" name="userfile" type="file" /></td></tr>
    			<tr><td></td><td>*Note* your resume must be in either a Word Document or PDF file format to upload correctly.</td></tr>
				</table>
				<button type="submit" class="submit">Upload Resume</button>
				<button type="button" class="cancel" onclick="document.location.href='<?php BASE_URL;?>';">Cancel</button>
				</fieldset>	
		</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>