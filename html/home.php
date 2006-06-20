<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");

		if (isset($_SESSION['USER'])) 
		{
			if (in_array("Administrator",$_SESSION['USER']->getRoles())) 
			{
				$href = "commissionsEditForm.php";
				$header = "<tr><th></th><th>Edit Commission</th><th>Edit Vacancy</th></tr>";
				$v_start = "<a href=\"#\">Edit ";
				$v_end = "</a>";
				$add = "<div class=\"titleBar\"><button type=\"button\" class=\"addSmall\" onclick=\"document.location.href='committees&#47;addCommitteeForm.php';\">Add</button>Board or Commission</div>";
				$edit = "<button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='committees&#47;updateCommitteeForm.php?id=";
				$edit_end = "'\">Edit</button>";
				$delete =	"<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('committees&#47;deleteCommittee.php?id=";
				$delete_end = "');\">Delete</button>";
			}
			else 
			{
				$href = "commissionsEditForm.php";
				$header = "<tr><th></th><th>Edit Commission</th><th>Edit Vacancy</th></tr>";
				$v_start = "<a href=\"#\">Edit ";
				$v_end = "</a>";
				$add = "";
				$edit = "";
				$edit_end = "";
				$delete = "";
				$delete_end = "";
			}
		}
		else 
		{
			$href = "commissions.php"; 
			$header = "<tr><th></th><th>Boards &amp; Commissions</th><th>Vacancy?</th></tr>";
			$v_start = "";
			$v_end = "";
			$add = "";
			$edit = "";
			$edit_end = "";
			$delete = "";
			$delete_end = "";
		}
		
		$commissionList = new CommissionList();
		$commissionList->find();
		
		echo "{$add}<table>{$header}";
		foreach($commissionList as $commission) 
		{
			$vacancy = "";
			$seatList = new SeatList(array("commission_id"=>$commission->getId()));
			foreach($seatList as $seat)
			{
				if ($seat->getVacancy() == 1) { $vacancy = "Position Available"; }
			}
			if ($edit == "" && $delete == "") { $id = "";}
			else { $id = $commission->getId(); }
			echo "<tr><td>{$edit}{$id}{$edit_end} {$delete}{$id}{$delete_end}</td>
								<td><a href=\"{$href}?id={$commission->getId()}\">{$commission->getName()}</a></td>
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