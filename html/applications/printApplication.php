<?php

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/printBanner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");

?>
<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc"); ?>
	<h1>Board And Commission Application</h1>
		<fieldset><legend>Application Info</legend>
			<table>
				<tr><th>Field Name</th><th>Personal Information</th></tr>
				<tr><td><label>Name</label></td>
						<td>_______________________</td>
				</tr>
				<tr><td><label>Email Address</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>Home Phone Number</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>Work Phone Number</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>Occupation</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td>----------Address-----------</td></tr>
				<tr><td><label>Address</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>City, State</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>Zipcode</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td><label>City Resident?</label></td>
						<td>_______________________</td>
				</tr>
				
				<tr><td>---------------------------------</td></tr>
			<tr><td><label>Board/Commission:</label></td>
				<td>_______________________</td></tr> 
		</table>
		<table>
				<tr><td>----------------------------------</td></tr>
				<tr><td><label>Interest in this Board/Commission:</label></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>


				<tr><td><label>Qualifications:</label></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
				<tr><td></td></tr>
				<tr><td>_____________________________________________________________________</td></tr>
			</table>
			<p>If you wish to mail this form, please address it as follows:</p>

			<p>Office of the Clerk<br />
				 City of Bloomington<br />
				 401 N. Morton Street<br />
				 Bloomington, IN 47404<p/>
		</fieldset>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>