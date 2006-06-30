<?php

/*
	$_POST variables:	firstname
										lastname
										email
										resident
										interest
										qualifications
										
										#Optional
										address
										city
										zipcode
										homephone
										workphone
										occupation
*/

	#--------------------------------------------------------------------------
	# Create a new application
	#--------------------------------------------------------------------------
	$application = new Application();
	$application->setFirstname($_POST['firstname']);
	$application->setLastname($_POST['lastname']);
	$application->setEmail($_POST['email']);
	$application->setInterest($_POST['interest']);
	$application->setQualifications($_POST['qualifications']);
	$application->setTimestamp(date("Y-m-d H:i:s", time()));
	
	$committee = new Committee($_POST['committee']);
	$application->setCommitteeId($committee->getId());
	
	if($_POST['resident'] && $_POST['resident'] == 'Yes') 
	{
		$application->setResident(1);
	}
	else
	{
		$application->setResident(0);
	}
	
	if ($_POST['address']) { $application->setAddress($_POST['address']); }
	if ($_POST['city']) { $application->setCity($_POST['city']); }
	if ($_POST['zipcode']) { $application->setZipcode($_POST['zipcode']); }
	if ($_POST['homephone']) { $application->setHomePhone($_POST['homephone']); }
	if ($_POST['workphone']) { $application->setWorkPhone($_POST['workphone']); }
	if ($_POST['occupation']) { $application->setOccupation($_POST['occupation']); }
	
	try
	{
		$application->save();
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: applicationForm.php");
	}
	#--------------------------------------------------------------------------
	# Upload Resume
	#--------------------------------------------------------------------------
	$newdir = mkdir(APPLICATION_HOME.'/resumes/'.$application->getId());
	$uploaddir = APPLICATION_HOME.'/resumes/'. $application->getId().'/';
	$file = basename($_FILES['userfile']['name']);
	$file = explode(".", $file);
	$file = "resume." . $file[1];
	$uploadfile = $uploaddir . $file;
	
	if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) 
	{
		$application->gzip($uploadfile, $uploadfile . ".gz");
		unlink($uploadfile);
		$application->setResumePath($uploadfile. ".gz");
	}
	else {Header("Location: applicationForm.php?err={$_FILES['userfile']['error']}");}
	
	try
	{
		$application->save();
		Header("Location: ". BASE_URL);
	}
	catch (Exception $e)
	{
		$_SESSION['errorMessages'][] = $e;
		Header("Location: applicationForm.php");
	}
?>


