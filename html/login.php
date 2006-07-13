<?php
/*
	Logs a user into the system.
	A logged in user will have a $_SESSION['USER']
								$_SESSION['IP_ADDRESS']
								$_SESSION['APPLICATION_NAME']


	$_POST Variables:	username
						password
						returnURL
*/
	try
	{
		$user = new User($_POST['username']);

		if ($user->authenticate($_POST['password'])) { $user->startNewSession(); }
		else { throw new Exception("wrongPassword"); }
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: ".$_POST['returnURL']);
		exit();
	}

	Header("Location: " .BASE_URL);
?>