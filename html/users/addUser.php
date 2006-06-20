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
	$user = new User();
	$user->setAuthenticationMethod($_POST['authenticationMethod']);
	$user->setUsername($_POST['uname']);
	if ($_POST['pword']) { $user->setPassword($_POST['pword']); }
	if (isset($_POST['roles'])) { $user->setRoles($_POST['roles']); }

	if ($_POST['authenticationMethod'] == "LDAP")
	{
		# Load the rest of their stuff from LDAP
		require_once(GLOBAL_INCLUDES."/classes/LDAPEntry.inc");
		$ldap = new LDAPEntry($user->getUsername());
		$user->setFirstname($ldap->getFirstname());
		$user->setLastname($ldap->getLastname());
		$user->setEmail($ldap->getEmail());
		$user->setHomephone($ldap->setHomephone());
	}
	else
	{
		$user->setFirstname($_POST['firstname']);
		$user->setLastname($_POST['lastname']);
	}

	try
	{
		$user->save();
		Header("Location: home.php");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addUserForm.php");
	}
?>