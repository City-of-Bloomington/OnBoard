<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>

	<h1>Edit Committee</h1>
	<form method="post" action="updateSeat.php">
	<fieldset><legend>Seat Info</legend>
		<input name="id" type="hidden" value="<?php $seat = new Seat($_GET['id']);
		 echo $seat->getId(); ?>" />
		<table>
		<tr><td><label for="title">Seat Title</label></td>
			<td><input name="title" id="title" value="<?php echo $seat->getTitle(); ?>" /></td></tr>
				
		<tr><td><label for="vacant">Vacant?</label></td>
				<td><input type="checkbox" name="vacant" id="vacant"
					<?php  if ($seat->getVacancy() == 1){ echo "checked=\"checked\"";} ?> /></td>
		</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='home.php';">Cancel</button>
	</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>