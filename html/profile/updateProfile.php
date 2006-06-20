<?php
/*
	$_POST variables:	id
										uname
										pword
										fname
										lname
										email
										homephone
										workphone
										city
										zip
										about
			
*/
	verifyUser("Committee Member", "Administrator");

	#--------------------------------------------------------------------------
	# Update the account
	#--------------------------------------------------------------------------
	$user = new User($_POST['id']);
	if ($user->getAuthenticationMethod() == "local") 
	{
		$user->setUsername($_POST['uname']);
		if ($_POST['pword']) { $user->setPassword($_POST['pword']); }
	}
	if ($_POST['fname']) { $user->setFirstname($_POST['fname']); }
	if ($_POST['lname']) { $user->setLastname($_POST['lname']); }
	if ($_POST['email']) { $user->setEmail($_POST['email']); }
	if ($_POST['homephone']) { $user->setHomephone($_POST['homephone']); }
	if ($_POST['workphone']) { $user->setWorkphone($_POST['workphone']); }
	if ($_POST['street']) { $user->setStreet($_POST['street']); }
	if ($_POST['city']) { $user->setCity($_POST['city']); }
	if ($_POST['zip']) { $user->setZipcode($_POST['zip']); }
	if ($_POST['about']) { $user->setAbout($_POST['about']); }
	
	try
	{
		$user->save();
	  Header("Location: " . BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateProfileForm.php");
	}
?>