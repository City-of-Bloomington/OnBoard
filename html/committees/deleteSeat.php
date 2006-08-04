<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete seat
	#--------------------------------------------------------------------------

	if ($_GET['id'] != $_SESSION['USER']->getId()) 
	{
		$seat = new Seat($_GET['id']);
		$seat->deleteSeat();
	}
	
		Header("Location: home.php");
	
?>
