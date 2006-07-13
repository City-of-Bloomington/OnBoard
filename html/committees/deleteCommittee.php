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
		$committee = new Committee($_GET['id']);
		$seatList = new SeatList(array("committee_id"=>$committee->getId()));
		foreach ($seatList as $seat) { $seat->deleteSeat(); }
		$committee->deleteCommittee();
	}
	
		Header("Location: ". BASE_URL);
	
?>