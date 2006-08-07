<?php
/*
	$_GET variables:	id
*/

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");

?>
<div id="mainContent">
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");
		$application = new Application($_GET['id']); 
	?>
	<h1><?php echo $application->getFirstname()." ".$application->getLastname(); ?>'s Application</h1>
		<fieldset><legend>Application Info</legend>
			<input name="id" type="hidden" value="<?php echo $application->getId(); ?>" />
			<table>
				<tr><th>Field Name</th><th>Personal Information</th></tr>
				<tr><td><label>Name</label></td>
						<td><?php echo $application->getFirstname()." ".$application->getLastname(); ?></td>
				</tr>
				<tr><td><label>Email Address</label></td>
						<td><?php echo "<a href='mailto:{$application->getEmail()}'>{$application->getEmail()}</a>" ; ?></td>
				</tr>
				
				<tr><td><label>Home Phone Number</label></td>
						<td><?php echo $application->getHomePhone(); ?></td>
				</tr>
				
				<tr><td><label>Work Phone Number</label></td>
						<td><?php echo $application->getWorkPhone(); ?></td>
				</tr>
				
				<tr><td><label>Occupation</label></td>
						<td><?php echo $application->getOccupation(); ?></td>
				</tr>
				
				<tr><td>----------Address-----------</td></tr>
				<tr><td><label>Address</label></td>
						<td><?php echo $application->getAddress(); ?></td>
				</tr>
				
				<tr><td><label>City, State</label></td>
						<td><?php echo $application->getCity(); ?></td>
				</tr>
				
				<tr><td><label>Zipcode</label></td>
						<td><?php echo $application->getZipcode(); ?></td>
				</tr>
				
				<tr><td><label>City Resident?</label></td>
						<td><?php if($application->getResident() == 1) { echo "Yes"; }
											else {echo "No";} ?></td>
				</tr>
				
				<tr><td>---------------------------------</td></tr>
				<tr><td><label>Board/Commission Applied For:</label></td>
				<?php 
					 $committee = new Committee($application->getCommitteeId());
					 echo "<td>{$committee->getName()}</td></tr>"; 
				?>
				<tr><td>----------------------------------</td></tr>
				<tr><td><label>Interest in this Board/Commission:</label></td>
						<td><?php echo $application->getInterest(); ?></td>
				</tr>
				<tr><td>----------------------------------</td></tr>
				<tr><td><label>Qualifications:</label></td>
						<td><?php echo $application->getQualifications(); ?></td>
				</tr>
				<tr><td><label>Link to Resume:</label></td>
						<td><?php if ($application->getResumePath()) { echo "<a href=\"download.php?id={$application->getId()}\">Resume</a>";}
											else { echo "No resume attached."; } ?></td>
				</tr>
				<tr><td><label>Application Date/Time:</label></td>
						<td><?php  echo $application->getTimestamp(); 
							
							# Compare the current timestamp and the application timestamp
							# If there there is a year or more difference the message below will display
							if ($application->timestampCheck($application->getTimestamp())) 
							{ 
								echo "<h5><b>{$application->getFirstname()} {$application->getLastname()}'s application has been on file for over a year.
											<br />Click <a href='mailto:{$application->getEmail()}'>here</a> to email applicant.</h5>";
							}?></td>
				</tr>
			</table>
		</fieldset>
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>