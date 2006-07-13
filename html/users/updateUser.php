<?php
/*
	$_POST variables:	id
						authenticationMethod
						username
						roles
						firstname
						lastname

						# Optional
						password
						email
						homephone
						workphone
						city
						address
						zip
*/
	verifyUser("Administrator", "Committee Member");

	#--------------------------------------------------------------------------
	# Update the account
	#--------------------------------------------------------------------------
	$user = new User($_POST['id']);
	
	#Only updated when Admin accesses an account other than their own
	if ($_POST['username']) { $user->setUsername($_POST['username']); $direct = "home.php";}
	else { $direct = BASE_URL; }
	if ($_POST['authenticationMethod']) {$user->setAuthenticationMethod($_POST['authenticationMethod']); }
	
	$user->setFirstname($_POST['firstname']);
	$user->setLastname($_POST['lastname']);
	$user->setEmail($_POST['email']);
	$user->setHomephone($_POST['homephone']);
	$user->setWorkphone($_POST['workphone']);
	$user->setCity($_POST['city']);
	$user->setAddress($_POST['address']);
	$user->setZipCode($_POST['zip']);
	$user->setAbout($_POST['about']);
	$user->setTimestamp(date("Y-m-d H:i:s",time()));
	

	# Only update the password if they actually typed somethign in
	if ($_POST['password']) { $user->setPassword($_POST['password']); }
	if (isset($_POST['roles'])) { $user->setRoles($_POST['roles']); }


	try
	{
		$user->save();
		Header("Location: ". $direct);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateUserForm.php?id={$user->getId()}");
	}
?>