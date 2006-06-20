<?php
/*
	$_POST variables:	authenticationMethod
						username
						roles

						# May be optional if LDAP is used
						password

*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Create a new seat
	#--------------------------------------------------------------------------
	$seat = new Seat();
	$seat->setTitle($_POST['title']);
	if (isset($_POST['restrictions'])) { $seat->setRestrictions($_POST['restrictions']); }
	
	if (isset($_POST['category']))
	{
		$category = new SeatCategory($_POST['category']);
		$seat->setCategory($category);
	}
	
	if (isset($_POST['id']))
	{
		$commission = new Commission($_POST['id']);
		$seatList = new SeatList(array("commission_id"=>$commission->getId()));
		if (count($seatList) != $commission->getCount()) {$seat->setCommission($commission);}
		else { throw new Exception("Too Many Seats on Commission");}
	}
	
	if (isset($_POST['users']))
	{
		if ($_POST['users'] == "--Vacant--") 
		{ 
			$seat->setVacancy(1); 
		}
		else 
		{
			$seat->setVacancy(0);
			$user = new User($_POST['users']);
			$seat->setTermStart($_POST['t_start']);
			$seat->setTermEnd($_POST['t_end']);
			$seat->setUser($user);
		}
	}
	
	try
	{
		$seat->save();
		Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addSeatForm.php");
	}
?>