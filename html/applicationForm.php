<?php

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>
	
	<h1>Board and Commission Application</h1>
	
	<p><b>Please complete the information below. When you are finished, press the "Submit" button to send your application.<br />
	Fields with an asterisk (*) are required fields.<br /></b>
	</p>
	<form method="post" action="application.php">
		<fieldset><legend>Basic Information</legend>
			<table>
				<tr>
					<td><label for="firstname">First Name*</label></td>
					<td><input size="33" name="firstname" id="firstname" /></td>
				</tr>
				<tr>
					<td><label for="lastname">Last Name*</label></td>
					<td><input size="33" name="lastname" id="lastname" /></td>
				</tr>
				<tr>
					<td><label for="email">Email Address*</label></td>
					<td><input size="33" name="email" id="email" /></td>
				</tr>
				<tr>
					<td><label for="date">Today's Date*</label></td>
					<td><select name="date" id="date">
							<?php for($i = 1; $i < 32; $i++) 
							{ 
								if ($i == date('j')) { $selected = "selected=\"selected\"";}
								else { $selected = ""; }
								echo "<option $selected>$i</option>"; 
							} 
								?>
						</select>			
						<select name="month" id="month">
							<?php
							$months = array("January","February","March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); 
							for($i = 0; $i < count($months); $i++) 
							{ 
								if ($months[$i] == date('F')) { $selected = "selected=\"selected\"";}
								else { $selected = ""; }
								echo "<option $selected>{$months[$i]}</option>"; 
							} ?>
						</select>				
						<select name="year" id="year">
							<?php
							for($i = 6; $i < 11; $i++) 
							{ 
								if ($i < 10) { $year = 0 . $i; } 
								else { $year = $i;}
								if ($year == date('y')) { $selected = "selected=\"selected\"";}
								else { $selected = ""; }
								echo "<option $selected>20{$year}</option>"; 
							} 
								?>
						</select>				
					</td>
				</tr>
				<tr>
					<td><label for="address">Address</label></td>
					<td><input size="33" name="address" id="address" /></td>
				</tr>
				<tr>
					<td><label for="city">City</label></td>
					<td><input size="33" name="city" id="city" /></td>
				</tr>
				<tr>
					<td><label for="zip">Zipcode</label></td>
					<td><input size="33" name="zip" id="zip" /></td>
				</tr>
				<tr>
					<td><label for="dayphone">Day Phone Number</label></td>
					<td><input size="33" name="dayphone" id="dayphone" /></td>
				</tr>
				<tr>
					<td><label for="evenphone">Evening Phone Number</label></td>
					<td><input size="33" name="evenphone" id="evenphone" /></td>
				</tr>
				<tr>
					<td><label for="occupation">Occupation</label></td>
					<td><input size="33" name="occupation" id="occupation" /></td>
				</tr>
			</table>
			<table>
				<tr><td>City Boards and Commissions require City of Bloomington Residency.</td></tr>
				<tr>
					<td><label for="no_resident">Do you live in Bloomington City Limits?</label> 
							Yes<input type="radio" name="resident" id="yes_resident" /> 
							No<input type="radio" name="resident" id="no_resident" /></td>
				</tr>
			</table>
		</fieldset>
		<p></p>
		<fieldset><legend>Board/Commission Information</legend>
			<table>
				<tr><td><label for="commissions">Board or Commission for which you are applying:*</label></td></tr>
				<tr><td><select name="commissions" id="commissions">
							<?php 
							$commissionList = new CommissionList();
							$commissionList->find();
							foreach($commissionList as $commission) { echo "<option>{$commission->getName()}</option>"; } ?>
						</select></td>
				</tr>
				<tr><td><label for="interest">Please explain your interest in this position:*</label></td></tr>
				<tr><td><textarea cols="40" rows="5" id="interest"></textarea></td></tr>
				<tr><td><label for="qualifications">Please describe your qualifications for this position:*</label></td></tr>
				<tr><td><textarea cols="40" rows="5" id="qualifications"></textarea></td></tr>
				<tr><td>Submit your <a href="resumeForm.php">resume</a>.</td></tr>
			</table>
			<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='home.php';">Cancel</button>
		</fieldset>
	</form>
	<h5>*Required to submit form.</h5>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>