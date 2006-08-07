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
	# Create a new application (if there was no upload error)
	#
	# If there was an upload error the id of the application where the error 
	# occured will be sent here and the application will be loaded.
	#--------------------------------------------------------------------------
	if (!isset($_GET['id']))
	{ 
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
		else { $application->setResident(0); }
	
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
	}
	else { $application = new Application($_GET['id']); }
	#--------------------------------------------------------------------------
	# Upload Resume
	#--------------------------------------------------------------------------
	if ($_FILES['userfile']['size'] > 0)
	{
		$file = basename($_FILES['userfile']['name']);
		$file = explode(".", $file);
		$ext = $file[1];
		$file = "resume." . $ext;
		
		# Check file extension
		if ($ext == "doc" || $ext == "pdf")
		{
			$newdir = mkdir(APPLICATION_HOME.'/resumes/'.$application->getId());
			$uploaddir = APPLICATION_HOME.'/resumes/'. $application->getId().'/';
			$uploadfile = $uploaddir . $file;	
			# Upload the file
			if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) 
			{
				# If upload was successfull then compress the file, delete the old one 
				# and set the resume path to the new gzipped file
				$application->gzip($uploadfile, $uploadfile.".gz");
				unlink($uploadfile);
				$application->setResumePath($uploadfile.".gz");
				try
				{
					$application->save();
					Header("Location: submitted.php");
				}
				catch (Exception $e)
				{
					$_SESSION['errorMessages'][] = $e;
					Header("Location: applicationForm.php");
				}
			}
			# Upload error
			else {Header("Location: uploadResume.php?err={$_FILES['userfile']['error']}");}
		}
		# Incorrect file extension
		else { Header("Location: uploadResume.php?err=5&id={$application->getId()}");}
	} 
	else {Header("Location: uploadResume.php?err={$_FILES['userfile']['error']}&id={$application->getId()}");}
	
	
?>


