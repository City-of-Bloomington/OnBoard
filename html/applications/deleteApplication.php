<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete application
	#--------------------------------------------------------------------------

	if ($_GET['id']) 
	{
		$application = new Application($_GET['id']);
		$application->deleteApplication();
	}
	
		Header("Location: home.php");
	
?>