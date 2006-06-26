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
				$href = "commissions.php";
				$add = "<div class=\"titleBar\"><button type=\"button\" class=\"addSmall\" onclick=\"document.location.href='committees&#47;addCommitteeForm.php';\">Add</button>Board or Commission</div>";
				$edit = "<button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='committees&#47;updateCommitteeForm.php?id=";
				$edit_end = "'\">Edit</button>";
				$delete =	"<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('committees&#47;deleteCommittee.php?id=";
				$delete_end = "');\">Delete</button>";
			}
			else 
			{
				$href = "commissions.php"; 
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
			$add = "";
			$edit = "";
			$edit_end = "";
			$delete = "";
			$delete_end = "";
		}
		
		$commissionList = new CommissionList();
		$commissionList->find();
		
		echo "{$add}<table><tr><th></th><th>Boards &amp; Commissions</th><th>Vacancy?</th></tr>";
		foreach($commissionList as $commission) 
		{
			$vacancy = "";
			$seatList = new SeatList(array("commission_id"=>$commission->getId()));
			foreach($seatList as $seat)
			{
				if ($seat->getVacancy() == 1) 
				{ 
					$vacancy = "<a href=\"applicationForm.php\" onclick=\"window.open(this.href,'_blank');return false;\">Position Available</a>"; 
				}
			}
			if ($edit == "" && $delete == "") { $id = "";}
			else { $id = $commission->getId(); }
			echo "<tr><td>{$edit}{$id}{$edit_end} {$delete}{$id}{$delete_end}</td>
								<td><a href=\"{$href}?id={$commission->getId()}\">{$commission->getName()}</a></td>
								<td>$vacancy</td>
						</tr>";
		}
	
	?>
	</table>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>