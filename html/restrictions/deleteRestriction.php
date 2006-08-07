<?php
/*
	$_GET variables:	id
									
*/
	verifyUser("Administrator");

	#--------------------------------------------------------------------------
	# Delete restricion
	#--------------------------------------------------------------------------


	if ($_POST['restriction']) 
	{
		$restriction = new Restriction($_POST['restriction']);
		$restriction->deleteRestriction();
	}
	
	# Necessary to route user to correct forms after deleting a restriction
	if ($_GET['page'] == "add") {Header("Location: ".BASE_URL."/committees/addSeatForm.php?id=".$_GET['id']);}
	else {Header("Location: ".BASE_URL."/committees/updateSeatForm.php?seat={$_GET['page']}&id=".$_GET['id']);}
	
?>