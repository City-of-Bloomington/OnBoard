<?php
	verifyUser("Administrator");

	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar">
			Seats
		</div>
		<table>
		<tr><th>Title</th><th>Board/Commission</th><th>Appointment</th><th>Vacant?</th><th>Restrictions</th></tr>
		<?php
			
			# Find all the applications and sort by vacancy
			$seatList = new SeatList();
			$seatList->find(null, "committee_id");
			foreach($seatList as $seat)
			{
				# Display all seats
				echo "
					<td><a href=\"updateCommitteeForm.php?id={$seat->getCommittee()->getId()}\">{$seat->getTitle()}</a></td>
					<td>{$seat->getCommittee()->getName()}</td>
					<td>{$seat->getAppointment()->getName()}</td>";
					if ($seat->getVacancy() == 1) 
					{ 
						$vacant = "<a href=\"".BASE_URL."/applications/home.php?id={$seat->getCommittee()->getId()}\">Vacant</a>"; 
					}
					else { $vacant = "<a href=\"".BASE_URL."/viewProfile.php?id={$seat->getUser()->getId()}\">{$seat->getUser()->getFirstname()} {$seat->getUser()->getLastname()}</a>"; }
					echo "<td>{$vacant}</td>
					<td>";
					foreach($seat->getRestrictions() as $restriction) { echo "$restriction "; }
					echo "</td></tr>";
			}
		?>
		</table>
	</div>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>