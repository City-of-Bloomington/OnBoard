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
			<button type="button" class="addSmall" onclick="document.location.href='addSeatForm.php';">Add</button>
			Seats
		</div>
		<table>
		<tr><th></th><th>Title</th><th>Board/Commission</th><th>Appointment Type</th><th>Vacancy?</th><th>Restrictions</th></tr>
		<?php
			$seatList = new SeatList();
			$seatList->find(null, "commission_id");
			foreach($seatList as $seat)
			{
				echo "
				<tr><td><button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='updateSeatForm.php?id={$seat->getId()}'\">Edit</button>
						<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteSeat.php?id={$seat->getId()}');\">Delete</button>
					</td>
					<td>{$seat->getTitle()}</td>
					<td>{$seat->getCommission()->getName()}</td>
					<td>{$seat->getCategory()->getCategory()}</td>
					<td>{$seat->getVacancy()}</td>
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