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
	<form enctype="multipart/form-data" method="post" action="application.php">
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
					<td><label for="address">Address</label></td>
					<td><input size="33" name="address" id="address" /></td>
				</tr>
				<tr>
					<td><label for="city">City</label></td>
					<td><input size="33" name="city" id="city" /></td>
				</tr>
				<tr>
					<td><label for="zipcode">Zipcode</label></td>
					<td><input size="33" name="zipcode" id="zipcode" /></td>
				</tr>
				<tr>
					<td><label for="homephone">Home Phone Number</label></td>
					<td><input size="33" name="homephone" id="homephone" /></td>
				</tr>
				<tr>
					<td><label for="workphone">Work Phone Number</label></td>
					<td><input size="33" name="workphone" id="workphone" /></td>
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
							Yes<input type="radio" name="resident" id="yes_resident" value="Yes" checked="checked" /> 
							No<input type="radio" name="resident" id="no_resident" value="No" /></td>
				</tr>
			</table>
		</fieldset>
		<p></p>
		<fieldset><legend>Board/Commission Information</legend>
			<table>
				<tr><td><label for="committee">Board or Commission for which you are applying:*</label></td></tr>
				<tr><td><select name="committee" id="committee">
							<?php 
							$committeeList = new CommitteeList();
							$committeeList->find();
							foreach($committeeList as $committee) { echo "<option>{$committee->getName()}</option>"; } ?>
						</select></td>
				</tr>
				<tr><td><label for="interest">Please explain your interest in this position:*</label></td></tr>
				<tr><td><textarea cols="40" rows="5" name="interest" id="interest"></textarea></td></tr>
				<tr><td><label for="qualifications">Please describe your qualifications for this position:*</label></td></tr>
				<tr><td><textarea cols="40" rows="5" name="qualifications" id="qualifications"></textarea></td></tr>
			</table>

  	  		<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
   			<table>	
   				<tr><td><label for="userfile">Upload Resume</label></td>
    				<td><input id="userfile" name="userfile" type="file" /></td></tr>
    			<tr><td></td><td>*Note* your resume must be in either a Word Document or PDF file format to upload correctly.</td></tr>
				</table>
				<button type="submit" class="submit">Submit Application</button>
			<button type="button" onclick="document.location.href='printApplication.php';">View Printable Application</button>
			<button type="button" class="cancel" onclick="document.location.href='home.php';">Cancel</button>
				</fieldset>
		</form>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>