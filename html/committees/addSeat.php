<?php
/*
	$_POST variables:	
						title
						id (committee id)
										
						# May be optional 
						restrictions
						appointment
						users 
						t_start
						t_end

*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Create a new seat
	#--------------------------------------------------------------------------
	$seat = new Seat();
	$seat->setTitle($_POST['title']);
	if (isset($_POST['restrictions'])) { $seat->setRestrictions($_POST['restrictions']); }
	
	if (isset($_POST['appointment']))
	{
		$appointment = new SeatAppointment($_POST['appointment']);
		$seat->setAppointment($appointment);
	}
	
	if (isset($_POST['id']))
	{
		$committee = new Committee($_POST['id']);
		$seatList = new SeatList(array("committee_id"=>$committee->getId()));
		try
		{
			 $seat->setCommittee($committee);
		}
		catch (Exception $e)
		{
			$_SESSION['errorMessages'][] = $e;
			Header("Location: udpateCommitteeForm.php?id={$committee->getId()}");
		}
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
			if ($_POST['t_end']) { $seat->setTermEnd($_POST['t_end']);}
			else { $seat->setTermEnd("Indefinite");}
			$seat->setUser($user);
		}
	}
	
	try
	{
		$seat->save();
		Header("Location: updateCommitteeForm.php?id={$committee->getId()}");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addSeatForm.php?id={$committee->getId()}");
	}
?>