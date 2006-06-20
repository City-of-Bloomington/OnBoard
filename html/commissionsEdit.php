<?php
/*
	$_GET variables:	id

*/

	#--------------------------------------------------------------------------
	# Edit Commission Page
	#--------------------------------------------------------------------------
	$commission = new Commission($_GET['id']);
	$commission->setInfo($_POST['info']);
	
	try
	{
		$commission->save();
		Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: commissionsEditForm.php");
	}
?>