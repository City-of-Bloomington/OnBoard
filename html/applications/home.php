<?php
	/*
	
		GET variables: id
	
	*/
	
	verifyUser("Administrator", "Committee Member");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");

?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar">Applications</div>
		<table>
		<tr><th></th><th>Name</th><th>Board/Commission</th><th>Date &amp; Time Created</th></tr>
		<?php
			$applicationList = new ApplicationList();
			$applicationList->find();
			foreach($applicationList as $application)
			{
				$committee = new Committee($application->getCommitteeId());
				if (isset($_GET['id']))
				{
					$committee_id = $_GET['id'];
					if ($committee_id == $application->getCommitteeId()) 
					{
					echo "
					<tr><td>
						<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteApplication.php?id={$application->getId()}');\">Delete</button>
						</td>
						<td>{$application->getFirstname()} {$application->getLastname()}</td>
						<td>{$committee->getName()}</td>
						<td>{$application->getTimestamp()}</td>
						<td><a href=\"viewApplication.php?id={$application->getId()}\">view</a></td>
					</tr>";
					}
				}
				else 
				{
					echo "
					<tr><td>
						<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteApplication.php?id={$application->getId()}');\">Delete</button>
						</td>
						<td>{$application->getFirstname()} {$application->getLastname()}</td>
						<td>{$committee->getName()}</td>
						<td>{$application->getTimestamp()}</td>
						<td><a href=\"viewApplication.php?id={$application->getId()}\">view</a></td>
					</tr>";
				}
			}
		?>
		</table>
	</div>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>