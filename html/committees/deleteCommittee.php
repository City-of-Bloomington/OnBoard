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
		$seatList = new SeatList(array("commission_id"=>$commission->getId()));
		foreach ($seatList as $seat) { $seat->deleteSeat(); }
		$commission->deleteCommission();
	}
	
		Header("Location: home.php");
	
?>