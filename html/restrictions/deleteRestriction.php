<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete restricion
	#--------------------------------------------------------------------------

	if ($_POST['restrictions']) 
	{
		$restriction = new Restriction($_POST['restrictions']);
		$restriction->deleteRestriction();
	}
	
		Header("Location: home.php");
	
?>