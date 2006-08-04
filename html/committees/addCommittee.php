<?php
/*
	$_POST variables:	name
										member_count
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Create the new committee
	#--------------------------------------------------------------------------
	$committee = new Committee();
	$committee->setName($_POST['name']);
	$committee->setCount($_POST['member_count']);

	try
	{
		$committee->save();
		Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: addCommitteeForm.php");
	}
?>