<div id="sidebar">
<?php
	if (!isset($_SESSION['USER'])) { include(GLOBAL_INCLUDES."/sidebarBoxes/loginBox.inc"); }
	else
	{
		if (in_array("Committee Member",$_SESSION['USER']->getRoles())) {include(APPLICATION_HOME."/includes/sidebarBoxes/Profile.inc"); }
		if (in_array("Administrator",$_SESSION['USER']->getRoles())) { include(APPLICATION_HOME."/includes/sidebarBoxes/Administrator.inc"); }
	}
?>
</div>