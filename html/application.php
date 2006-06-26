<?php

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>
	
	<form>
		<fieldset><legend>Application</legend>
			<table>
				<tr>
					<td><label for="firstname">First Name*</label></td>
					<td><input name="firstname" id="firstname" /></td>
				</tr>
				<tr>
					<td><label for="lastname">Last Name*</label></td>
					<td><input name="lastname" id="lastname" /></td>
				</tr>
				<tr>
					<td><label for="email">Email Address*</label></td>
					<td><input name="email" id="email" /></td>
				</tr>
				<tr>
					<td><label for="date">Today's Date*</label></td>
					<td><select name="date" id="date">
							<?php for($i = 1; $i < 32; $i++) { echo "<option>$i</option>"; } ?>
						</select>			
						<select name="month" id="month">
							<?php
							$months = array("January","February","March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); 
							for($i = 0; $i < count($months); $i++) { echo "<option>{$months[$i]}</option>"; } ?>
						</select>				
						<select name="year" id="year">
							<?php
							for($i = 6; $i < 10; $i++) { echo "<option>200{$i}</option>"; } ?>
						</select>				
					</td>
				</tr>
				<tr>
					<td><label for="address">Address</label></td>
					<td><input name="address" id="address" /></td>
				</tr>
				<tr>
					<td><label for="city">City</label></td>
					<td><input name="city" id="city" /></td>
				</tr>
				<tr>
					<td><label for="zip">Zipcode</label></td>
					<td><input name="zip" id="zip" /></td>
				</tr>
				<tr>
					<td><label for="dayphone">Day Phone Number</label></td>
					<td><input name="dayphone" id="dayphone" /></td>
				</tr>
				<tr>
					<td><label for="evenphone">Evening Phone Number</label></td>
					<td><input name="evenphone" id="evenphone" /></td>
				</tr>
			</table>
			<table>
				<tr><td><label for="yes_resident">City Boards and Commissions require City of Bloomington Residency.</label></td></tr>
				<tr>
					<td><label for="no_resident">Do you live in Bloomington City Limits?</label> 
						Yes<input type="radio" name="resident" id="yes_resident" /> 
						No<input type="radio" name="resident" id="no_resident" /></td>
				</tr>
			</table>
			<table>
				<tr>
					<td><label for="occupation">Occupation</label></td>
					<td><input name="occupation" id="occupation" /></td>
				</tr>
				<tr>
					
				</tr>
			</table>
		</fieldset>
	</form>
	
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>