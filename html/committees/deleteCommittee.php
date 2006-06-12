<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete account
	#--------------------------------------------------------------------------

	if ($_GET['id'] != $_SESSION['USER']->getId()) 
	{
		$commission = new Commission($_GET['id']);
		$commission->deleteCommission();
	}
	
		Header("Location: home.php");
	
?>