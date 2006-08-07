<?php
	/*
		GET variables: id
	*/
	
	verifyUser("Administrator", "Committee Member");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
	
	if (isset($_GET['id'])) { $committee = new Committee($_GET['id']);  $name = $committee->getName();}
	else { $name = "";}
?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar"><?php echo $name;?> Applications</div>
		<table>
		<tr><th></th><th>Name</th><th>Board/Commission</th><th>Date &amp; Time Created</th></tr>
		<?php
			$count;
		
			# Find all the applications and sort by timestamp
			$applicationList = new ApplicationList();
			$applicationList->find(null, 'timestamp');
			foreach($applicationList as $application)
			{
				# Check to see if the application is a year or more old and the $flag variable is set.
				if ($application->timestampCheck($application->getTimestamp())) { $flag = "Over a year old.";}
				else {$flag = "";}
				$committee = new Committee($application->getCommitteeId());
				
				# If $_GET['id'] is set then each applications committee_id is compared to the id sent 
				if (isset($_GET['id']))
				{
					if ($_GET['id'] == $application->getCommitteeId()) 
					{
					echo "
					<tr><td>";
						# Delete buttons added to each application if user is Administrator
						if (in_array("Administrator", $_SESSION['USER']->getRoles()))
						{
							echo "<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteApplication.php?id={$application->getId()}');\">Delete</button>";
						} 
						
						# Application with the committee_id = to $_GET['id'] will display the following info
						echo "</td>
						<td>{$application->getFirstname()} {$application->getLastname()}</td>
						<td>{$committee->getName()}</td>
						<td>{$application->getTimestamp()}</td>
						<td><a href=\"viewApplication.php?id={$application->getId()}\">view</a></td>
						<td>$flag</td>
					</tr>";
					$count += 1;
					}
				}
				else 
				{
					# If $_GET['id'] is not set then all applications are displayed.
					echo "
					<tr><td>";
						
						# Delete buttons added to each application if user is Administrator
						if (in_array("Administrator", $_SESSION['USER']->getRoles()))
						{
							echo "<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteApplication.php?id={$application->getId()}');\">Delete</button>";
						}
						 
						# All applications are displayed with the following info.
						echo "</td>
						<td>{$application->getFirstname()} {$application->getLastname()}</td>
						<td>{$committee->getName()}</td>
						<td>{$application->getTimestamp()}</td>
						<td><a href=\"viewApplication.php?id={$application->getId()}\">view</a></td>
						<td>$flag</td>
					</tr>";
				}
			}
			echo "</table>";
			
			# If there aren't any applications for a particular board or commission the message below will display.
			if (!isset($count) && isset($_GET['id'])) 
			{ 
				echo "<table><tr><td><h3>No applications for this Board or Commission yet.</h3></td></tr></table>"; 
			}
		?>
	</div>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>