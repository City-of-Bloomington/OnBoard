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
		$user = new User($_GET['id']);
		$user->deleteUser();
	}
	
		Header("Location: home.php");
	
?>