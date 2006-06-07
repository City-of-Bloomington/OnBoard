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
	$user->setUsername($_POST['username']);
	if ($_POST['password']) { $user->setPassword($_POST['password']); }
	if (isset($_POST['roles'])) { $user->setRoles($_POST['roles']); }

	if ($_POST['authenticationMethod'] == "LDAP")
	{
		# Load the rest of their stuff from LDAP
		require_once(GLOBAL_INCLUDES."/classes/LDAPEntry.inc");
		$ldap = new LDAPEntry($user->getUsername());
	}
	else
	{
		# Load any other fields from the form
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