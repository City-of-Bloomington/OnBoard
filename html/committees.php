<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
		<div class="titleBar">
			<?php $committee = new Committee($_GET['id']); 
						echo "{$committee->getName()}"; ?>
		</div>
		<?php
			
			$member = 0;
			
			# Find all seats associated with the committee associated with the $_GET['id']
			$seatList = new SeatList(array("committee_id"=>$committee->getId()));
			
			# If the user is logged in check each seat to see if the user is on that committee
			if (isset($_SESSION['USER']))
			{
				foreach($seatList as $seat) 
				{ 
					if ($seat->getVacancy() == 0)
					{
						if ($seat->getUser()->getId() == $_SESSION['USER']->getId()) { $member += 1; }
					}
				}
				# If the user is on that committee or user is administrator the user will be given
				# access to the FCKeditor, else show committee page
				if (in_array("Administrator", $_SESSION['USER']->getRoles()) || $member > 0) 
				{ 
					# FCKeditor
					include(FCK_EDITOR."/fckeditor.php");
					echo "<form action=\"committeePage.php?id={$committee->getId()}\" method=\"post\">";
					$oFCKeditor = new FCKeditor('editor');
					$oFCKeditor->BasePath = '/FCKeditor/';
					$oFCKeditor->Value = $committee->getInfo();
					$oFCKeditor->Config["CustomConfigurationsPath"] = BASE_URL."/committee_config.js";
					$oFCKeditor->Create();
					echo "<input type=\"submit\" value=\"Submit\"></form>";
				}
				else { echo $committee->getInfo();}
			}
			else { echo $committee->getInfo();} 
			
		?>
		<table>
			
			<?php
				
				# If there are more than 0 seats, display table header
				if (count($seatList) > 0 )
				{
					echo "<tr><th>Member</th><th>Appointment</th><th>Qualifications</th><th>Term End</th></tr>";
				}
				
				foreach ($seatList as $seat) 
				{
					# If a user occupies a seat give access to users profile and display user information
					if ($seat->getVacancy() == 0) 
					{ 
						$user = $seat->getUser()->getLastname() . ", " . $seat->getUser()->getFirstname(); 
						$term = $seat->getTermEnd();
						$href = "viewProfile.php?id={$seat->getUser()->getId()}"; 
					}
					# If a seat is vacant a link to the application form is given if the user is not logged in.
					# If user is logged in any application associated with this committee will be displayed.
					else 
					{ 
						$user = "vacant"; 
						$term = "";
						if (isset($_SESSION['USER'])) { $href="applications/home.php?id={$committee->getId()}"; }
						else { $href="applications/applicationForm.php\" onclick=\"window.open(this.href,'_blank');return false;";}
					}	
					
					# Display table with committee member information.
					echo "
						<tr>
						<td><a href=\"$href\">$user</a></td>
						<td>{$seat->getAppointment()->getName()}</td><td>";
						foreach($seat->getRestrictions() as $restriction) { echo "$restriction "; }
						echo "</td>
						<td>$term</td>
						</tr>";
				}
				
			?>
		</table>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>