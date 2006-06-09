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
	
	if ($_POST['vacant'] == "on") {$seat->setVacancy(1);}
	else {$seat->setVacancy(0);}
	
	if (isset($_POST['category']))
	{
		$category = new SeatCategory($_POST['category']);
		$seat->setCategory($category);
	}
	
	if (isset($_POST['commission']))
	{
		$commission = new Commission($_POST['commission']);
		$seat->setCommission($commission);
	}
	
	try
	{
		$seat->save();
		Header("Location: home.php");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addCommitteeForm.php");
	}
?>