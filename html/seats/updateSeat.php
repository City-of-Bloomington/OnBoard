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
	# Update the seat
	#--------------------------------------------------------------------------
	$seat = new Seat($_POST['id']);
	$seat->setTitle($_POST['title']);
	if (isset($_POST['vacant']) && $_POST['vacant'] == "on") 
	{
		$seat->setVacancy(1);
	}
	else {
		$seat->removeVacancy(1);
		$seat->addVacancy(0);
	}

	try
	{
		$seat->save();
		Header("Location: home.php");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addSeatForm.php");
	}
?>