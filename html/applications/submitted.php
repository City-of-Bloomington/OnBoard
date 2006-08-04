<?php
/*
	$_GET variables:	id
*/
	include(GLOBAL_INCLUDES."/xhtmlHeader.inc");
	include(APPLICATION_HOME."/includes/banner.inc");
	include(APPLICATION_HOME."/includes/menubar.inc");
	include(APPLICATION_HOME."/includes/sidebar.inc");
?>

<div id="mainContent">
	<?php include(GLOBAL_INCLUDES."/errorMessages.inc");?>

	<h3>Your application was successfully submitted.</h3>
	<br />
	Return to the <a href="<?php echo BASE_URL; ?>">home</a> page.
	
</div>

<?php
	include(APPLICATION_HOME."/includes/footer.inc");
	include(GLOBAL_INCLUDES."/xhtmlFooter.inc");
?>