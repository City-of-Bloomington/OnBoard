<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete committee and every seat associated with this committee
	#--------------------------------------------------------------------------

	if ($_GET['id'] != $_SESSION['USER']->getId()) 
	{
		$committee = new Committee($_GET['id']);
		
		# Find all seats associated with this committee and delete them
		$seatList = new SeatList(array("committee_id"=>$committee->getId()));
		foreach ($seatList as $seat) { $seat->deleteSeat(); }
		$committee->deleteCommittee();
	}
	
		Header("Location: ". BASE_URL);
	
?>