<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");

?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); 
	
		if (isset($_POST['seat']) && $_POST['seat'] != "No Seats") 
		{ 
			$seat = new Seat($_POST['seat']);	
			$id = $seat->getId();
			$title = $seat->getTitle();
			$page = $_POST['seat'];
		}
		else if (isset($_GET['seat']))
		{
			$seat = new Seat($_GET['seat']);	
			$id = $seat->getId();
			$title = $seat->getTitle();
			$page = $_GET['seat'];
		}
		else { Header("Location: updateCommitteeForm.php?id={$_GET['id']}");}
	?>

<h1>Edit Seat</h1>
	<form method="post" action="updateSeat.php">
	<fieldset><legend>Seat Info</legend>
		<input name="id" type="hidden" value="<?php echo $id; ?>" />
		<table>
		<tr><td><label for="title">Seat Title</label></td>
			<td><input name="title" id="title" value="<?php echo $title; ?>" /></td></tr>
				
		<tr><td><label for="users">Replace Member</label></td>
			<td><select name="users" id="users">
				<option <?php if ($seat->getVacancy() == 1) {echo "selected=\"selected\"";} ?>>--Vacant--</option>
				<?php
					$users = new UserList();
					$users->find();
					foreach($users as $user) { 
						if ($seat->getUser() == $user) { $selected = "selected=\"selected\""; }
						else { $selected = ""; }
						echo "<option $selected>{$user->getUsername()}</option>"; 
					}
				?>
				</select>
			</td>
		</tr>
		
		<tr><td><label for="t_start">Term Start Date</label></td>
		<td><input name="t_start" id="t_start" value="<?php echo $seat->getTermStart(); ?>" /></td></tr>
			
		<tr><td>*<label for="t_end">Term End Date</label></td>
		<td><input name="t_end" id="t_end" value="<?php echo $seat->getTermEnd(); ?>" /></td></tr>
		
		<tr><td><label for="restrictions">Restrictions</label>
			<button type="button" class="addSmall" onclick="document.location.href='<?php echo BASE_URL."/restrictions/addRestrictionForm.php?page={$page}&id={$seat->getId()}"; ?>'">Add</button>
			<button type="button" class="deleteSmall" onclick="document.location.href='<?php echo BASE_URL."/restrictions/deleteRestrictionForm.php?page={$page}&id={$seat->getId()}"; ?>'">Delete</button></td>
			<td><select name="restrictions[]" id="restrictions" size="5" multiple="multiple">
				<?php
					$restrictions = new RestrictionList();
					$restrictions->find();
					foreach($restrictions as $restriction) { echo "<option>$restriction</option>"; }
				?>
				</select>
			</td>
		</tr>
		<tr><td>*If term is indefinte leave field blank.</td></tr>
		
		</table>
	
		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo "updateCommitteeForm.php?id={$_GET['id']}"; ?>';">Cancel</button>
	</fieldset>
	</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>