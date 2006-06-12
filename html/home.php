<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<table>
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");

		if (isset($_SESSION['USER'])) 
		{
			$href = "commissionsEdit.php";
			$header = "<tr><th>Edit Commission</th><th>Edit Vacancy</th></tr>";
			$v_start = "<a href=\"#\">Edit ";
			$v_end = "</a>";
		}
		else 
		{
			$href = "commissions.php"; 
			$header = "<tr><th>Boards &amp; Commissions</th><th>Vacancy?</th></tr>";
			$v_start = "";
			$v_end = "";
		}
		
		$commissionList = new CommissionList();
		$commissionList->find();
		
		echo "{$header}";
		foreach($commissionList as $commission) 
		{
			$seatList = new SeatList(array("commission_id"=>$commission->getId()));
			$vacancy = "";
			foreach($seatList as $seat)
			{
				if ($seat->getVacancy() == 1) { $vacancy = "Position Available"; }
			}
			echo "<tr><td><a href=\"{$href}?id={$commission->getId()}\">{$commission->getName()}</a></td>
								<td>{$v_start}{$vacancy}{$v_end}</td>
						</tr>";
		}
	
	?>
	</table>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>