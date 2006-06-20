<?php
	if (!isset($_SESSION['USER'])) { include(GLOBAL_INCLUDES."/sidebarBoxes/loginBox.inc"); }
	else 
	{ 
		include(APPLICATION_HOME."/includes/sidebarBoxes/Profile.inc");
		if (in_array("Administrator",$_SESSION['USER']->getRoles())) { include(APPLICATION_HOME."/includes/sidebarBoxes/Administrator.inc"); } 
	}
?>