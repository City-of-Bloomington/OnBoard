<?php
/*
	$_POST variables:	restriction
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Create a new restriction
	#--------------------------------------------------------------------------
	$restriction = new Restriction();
	$restriction->setRestriction(sanitizeString($_POST['restriction']));
	
	try
	{
		$restriction->save();
		Header("Location: addSeatForm.php?id={$_GET['id']}");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addRestrictionForm.php?id={$_GET['id']}");
	}
?>