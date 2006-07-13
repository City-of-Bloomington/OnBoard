<?php

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
	include(APPLICATION_HOME."/includes/sidebarBoxes/RestrictionBox.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>
	
	<h1>Delete Restrictions</h1>
	<form method="post" action="deleteRestrictionForm.php?id=<?php if (isset($_GET['id'])) {echo $_GET['id']; } ?>">

	<fieldset><legend>Delete Restrictions</legend>
		<table>
			<tr><td><label for="restriction">Restrictions</label></td>
					<td><select name="restriction" id="restriction">
					<?php
						$restrictionList = new RestrictionList();
						if (count($restrictionList) == 0) { echo "<option>No Restrictions</option>";}
						else { foreach($restrictionList as $restriction) {  echo "<option>$restriction</option>";}}
					?>				
				</select>
				</td>
			</tr>
		</table>
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>';">Cancel</button>
	</fieldset>
	</form>
	</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>