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
	$commission = new Commission($_POST['id']);
	$commission->setName($_POST['name']);
	$commission->setCount($_POST['member_count']);

	try
	{
		$commission->save();
		Header("Location: home.php");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateCommitteeForm.php");
	}
?>