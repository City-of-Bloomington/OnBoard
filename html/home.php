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
			$href = '#';
			$header = "<tr><th>Edit Commission</th><th>Edit Vacancy</th></tr>";
			$vacancy = "<a href=#>Edit ";
			$v_end = "</a>";
		}
		else 
		{
			$href = '#';
			$header = "<tr><th>Board or Commission</th><th>Vacancy?</th></tr>";
			$vacancy = "";
			$v_end = "";
		}
		
		$commissionList = new CommissionList();
		$commissionList->find();
		echo "{$header}";
		foreach($commissionList as $commission) 
		{
			echo "<tr><td><a href=\"{$href}\">{$commission->getName()}</a></td>
								<td>{$vacancy}{$commission->getId()}{$v_end}</td>
						</tr>";
		}
	
	?>
	</table>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>