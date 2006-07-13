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
				$add = "<div class=\"titleBar\"><button type=\"button\" class=\"addSmall\" onclick=\"document.location.href='committees&#47;addCommitteeForm.php';\">Add</button>Board or Commission</div>";
				$edit = "<button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='committees&#47;updateCommitteeForm.php?id=";
				$edit_end = "'\">Edit</button>";
				$delete =	"<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('committees&#47;deleteCommittee.php?id=";
				$delete_end = "');\">Delete</button>";
			}
			else 
			{
				$add = "";
				$edit = "";
				$edit_end = "";
				$delete = "";
				$delete_end = "";
			}
		}
		else 
		{
			$add = "";
			$edit = "";
			$edit_end = "";
			$delete = "";
			$delete_end = "";
		}
	
		echo "{$add}<table><tr><th></th><th>Boards &amp; Commissions</th><th>Vacancy?</th></tr>";
	
		$committeeList = new CommitteeList();
		$committeeList->find();
		foreach($committeeList as $committee) 
		{
			$vacancy = "";
			$seatList = new SeatList(array("committee_id"=>$committee->getId()));
			foreach($seatList as $seat)
			{
				if ($seat->getVacancy() == 1) 
				{ 
					$applicationList = new ApplicationList(array("committee_id"=>$committee->getId()));
					if (count($applicationList) == 0 && isset($_SESSION['USER'])) { $vacancy = "Yes - No Applications";}
					else if (isset($_SESSION['USER'])) { $vacancy="<a href=\"applications/home.php?id={$committee->getId()}\">View Application(s)</a>"; }
					else { $vacancy="<a href=\"applications/applicationForm.php\" onclick=\"window.open(this.href,'_blank');return false;\">Position Available</a>";}
				}
			}
			if ($edit == "" && $delete == "") { $id = "";}
			else { $id = $committee->getId(); }
			echo "<tr><td>{$edit}{$id}{$edit_end} {$delete}{$id}{$delete_end}</td>
								<td><a href=\"committees.php?id={$committee->getId()}\">{$committee->getName()}</a></td>
						
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