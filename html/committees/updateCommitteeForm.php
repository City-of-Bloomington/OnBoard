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
	<form method="post" action="updateCommittee.php">
	<fieldset><legend>Committee Info</legend>
		<table>
			<input name="id" type="hidden" value="<?php $commission = new Commission($_GET['id']);
		 echo $commission->getId(); ?>" />
		<tr><td><label for="name">Committee Name</label></td>
			<td><input name="name" size="50" id="name" value="<?php echo $commission->getName(); ?>"/></td></tr>
	
		<tr><td><label for="member_count">Amount of Members</label></td>
			<td><select name="member_count" id="member_count" />
					<?php
						for ($i=0; $i<17; $i++) 
						{ 
							echo "<option";
							if ($i == $commission->getCount()) { echo " selected=\"selected\""; } 
							echo ">{$i}</option>"; 
						}
					?>				
				</select>
			</td>
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