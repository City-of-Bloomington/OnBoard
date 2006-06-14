<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
	
	verifyUser("Committee Member", "Administrator");
?>
<div id="mainContent">
	<div class="interfaceBox">
		<div class="titleBar">
			<?php $commission = new Commission($_GET['id']); 
						echo "{$commission->getName()}"; ?>
		</div>
		<table>
			<tr><th>Member</th><th>Appointment</th><th>Qualifications</th><th>Term End</th></tr>
			<?php
				$seatList = new SeatList(array('commission_id'=>$commission->getId()));
				foreach ($seatList as $seat) {
					if ($seat->getVacancy() == 1) { $user = "vacant"; }
					else { $user =  $seat->getUser()->getLastname() . ", " . $seat->getUser()->getFirstname(); }
					echo "
					<tr>
					<td>{$user}</td>
					<td>{$seat->getCategory()->getCategory()}</td><td>";
					foreach($seat->getRestrictions() as $restriction) { echo "{$restriction}, "; }
					echo "</td>
					<td>{$seat->getCommission()->getName()}</td>
				</tr>";
			}
		?>
		</table>
	</div>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>