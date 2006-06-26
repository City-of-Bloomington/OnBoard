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
	# Create the new account
	#--------------------------------------------------------------------------
	$committee = new Committee($_POST['id']);
	$committee->setName($_POST['name']);
	$committee->setCount($_POST['member_count']);
	
	if (isset($_POST['remove_seat']) && $_POST['remove_seat'] != "--Select Here--") 
	{ 
		$seat = new Seat($_POST['remove_seat']);
		$seat->deleteSeat();  
	}
	
	try
	{
		$committee->save();
		Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateCommitteeForm.php");
	}
?>