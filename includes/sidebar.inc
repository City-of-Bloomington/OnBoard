<div class="sidebar">
	<div class="titleBar">Tables</div>
	<ul>
	</ul>
</div>
<?php
	if (!isset($_SESSION['USER'])) { include(GLOBAL_INCLUDES."/sidebarBoxes/loginBox.inc"); }
	else { if (in_array("Administrator",$_SESSION['USER']->getRoles())) { include(APPLICATION_HOME."/includes/sidebarBoxes/Administrator.inc"); } }
?>