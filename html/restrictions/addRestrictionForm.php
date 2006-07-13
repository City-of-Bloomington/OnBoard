<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
	include(APPLICATION_HOME."/includes/sidebarBoxes/RestrictionBox.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>

	<h1>New Restriction</h1>
	<form method="post" action="addRestriction.php?id=<?php echo $_GET['id']; ?>">
		
	<fieldset><legend>Restriction Info</legend>
		<table>
			<tr>
					<td><label for="restriction">Restriction</label></td>
					<td><input name="restriction" id="restriction" /></td>
			</tr>	
		</table>
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo "addSeatForm.php?id={$committee->getId()}"; ?>';">Cancel</button>
	</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>