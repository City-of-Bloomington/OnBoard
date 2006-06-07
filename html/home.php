<?php
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>
<div id="mainContent">
	<?php
		include(GLOBAL_INCLUDES."/errorMessages.inc");

		# This line is just here for initial development to confirm you're logged in.
		# Delete it once things are working.
		if (isset($_SESSION['USER'])) { print_r($_SESSION['USER']); }
	?>
</div>
<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>