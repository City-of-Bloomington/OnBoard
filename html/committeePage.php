<?php
/*
	$_POST variables:	editor
	$_GET variables: id
*/
	verifyUser("Administrator", "Committee Member");

	#--------------------------------------------------------------------------
	# Dislay Committee Page from FCKeditor
	#--------------------------------------------------------------------------
	$committee = new Committee($_GET['id']);
	$committee->setInfo($_POST['editor']);

	try
	{
		$committee->save();
		Header("Location: committees.php?id={$_GET['id']}");
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: committees.php?id={$_GET['id']}");
	}
?>