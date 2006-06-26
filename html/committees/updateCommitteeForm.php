<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); 
				$committee = new Committee($_GET['id']); 
	?>
	<div class="titleBar">
			<?php echo 
			"<button type=\"button\" class=\"addSmall\" onclick=\"document.location.href='addSeatForm.php?id={$committee->getId()}';\">Add</button>"; 
			?>
		  Seat
	</div>
	<h1>Edit Committee</h1>
	<form method="post" action="updateCommittee.php">
	<fieldset><legend>Committee Info</legend>
		<input name="id" type="hidden" value="<?php echo $committee->getId(); ?>" />
		<table>
		<tr><td><label for="name">Committee Name</label></td>
			<td><input name="name" size="50" id="name" value="<?php echo $committee->getName(); ?>"/></td></tr>
	
		<tr><td><label for="remove_seat">Remove Seat</label></td>
			<td><select name="remove_seat" id="remove_seat">
					<option>--Select Here--</option>
					<?php
						$seatList = new SeatList(array("committee_id"=>$committee->getId()));
						foreach($seatList as $seat) {  echo "<option>{$seat->getTitle()}</option>";}
					?>				
				</select>
			</td>
		</tr>
	
		<tr><td><label for="member_count">Amount of Members</label></td>
			<td><select name="member_count" id="member_count" >
					<?php
						for ($i=0; $i<17; $i++) 
						{ 
							echo "<option";
							if ($i == $committee->getCount()) { echo " selected=\"selected\""; } 
							echo ">{$i}</option>"; 
						}
					?>				
				</select>
			</td>
		</tr>
	
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>';">Cancel</button>
	</fieldset>
	</form>
	
<h1>Edit Seat</h1>
	<form method="post" action="updateSeatForm.php">
	<fieldset><legend>Select Seat</legend>
		<table>
			<tr><td><label for="seat">Seat</label></td>
					<td><select name="seat" id="seat">
					<?php
						$seatList = new SeatList(array("committee_id"=>$committee->getId()));
						if (count($seatList) == 0) { echo "<option>No Seats</option>";}
						else { foreach($seatList as $seat) {  echo "<option>{$seat->getTitle()}</option>";}}
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