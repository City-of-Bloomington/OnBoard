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
		$seatList = new SeatList();
		$seatList->find();
		foreach($seatList as $seat) 
		{
			if ($seat->getUser()) 
			{
				if ($seat->getUser()->getId() == $user->getId()) { $seat->setVacancy(1); $seat->unsetUser($user); }
				$seat->save();
			}
		} 
		$user->deleteUser();
	}
	
		Header("Location: home.php");
	
?>