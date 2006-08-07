<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); 
	
		# Necessary to route user to correct forms after deleting a restriction
		if ($_GET['page'] == "add") { $cancel = BASE_URL."/committees/addSeatForm.php?id=".$_GET['id'];}
		else {$cancel = BASE_URL."/committees/updateSeatForm.php?seat={$_GET['page']}&id=".$_GET['id'];}
	?>

	<h1>New Restriction</h1>
	<form method="post" action="addRestriction.php?page=<?php echo $_GET['page']."&id=".$_GET['id']; ?>">
		
	<fieldset><legend>Restriction Info</legend>
		<table>
			<tr>
					<td><label for="restriction">Restriction</label></td>
					<td><input name="restriction" id="restriction" /></td>
			</tr>	
		</table>
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $cancel; ?>';">Cancel</button>
	</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>