<?php
/*
	$_POST variables:	id
						username
						admin
						info
						status
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Update the account
	#--------------------------------------------------------------------------
	$user = new User($_POST['id']);
	$user->setAuthenticationMethod($_POST['authenticationMethod']);
	$user->setUsername($_POST['uname']);
	if ($_POST['pword']) { $user->setPassword($_POST['pword']); }
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
	  Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: updateUserForm.php");
	}
?>