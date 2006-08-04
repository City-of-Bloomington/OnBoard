<?php
/*
	$_POST variables:	authenticationMethod
						username
						roles

						# May be optional if LDAP is used
						password
						restrictions

*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Update the seat
	#--------------------------------------------------------------------------
	$seat = new Seat($_POST['id']);
	$seat->setTitle($_POST['title']);
	if (isset($_POST['restrictions'])) { $seat->setRestrictions($_POST['restrictions']);}
	
	if (isset($_POST['users']))
	{
		if ($_POST['users'] == "--Vacant--") 
		{ 
			$seat->setVacancy(1); 
			$seat->unsetUser();
		}
		else 
		{
			$seat->removeVacancy(1);
			$seat->addVacancy(0);
			$user = new User($_POST['users']);
			$seat->setTermStart($_POST['t_start']);
			if ($_POST['t_end']) { $seat->setTermEnd($_POST['t_end']);}
			else { $seat->setTermEnd("Indefinite");}
			$seat->setUser($user);
		}
	}
	try
	{
		$seat->save();
		Header("Location: updateCommitteeForm.php?id={$seat->getCommitteeId()}");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateSeatForm.php");
	}
?>