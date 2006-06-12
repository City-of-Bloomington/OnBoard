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
			<button type="button" class="addSmall" onclick="document.location.href='addCommitteeForm.php';">Add</button>
			Board or Commission
		</div>
		<table>
			<tr><th></th><th>Commission</th><th>Member Total</th>
		<?php
			$commissionList = new CommissionList();
			$commissionList->find();
			foreach($commissionList as $commission)
			{
				echo "
				<tr><td><button type=\"button\" class=\"editSmall\" onclick=\"document.location.href='updateCommitteeForm.php?id={$commission->getId()}'\">Edit</button>
						<button type=\"button\" class=\"deleteSmall\" onclick=\"deleteConfirmation('deleteCommittee.php?id={$commission->getId()}');\">Delete</button>
					</td>
					<td>{$commission->getName()}</td>
					<td>{$commission->getCount()}</td>
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