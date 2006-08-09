<?php
/*

	GET variable: id (application id)

*/

verifyUser("Administrator", "Committee Member");

	if (isset($_GET['id'])) 
	{
		# Load application from id
		$application = new Application($_GET['id']);
		$path = $application->getResumePath();
		$file = basename($path);
		$file = explode(".", $file);
		$ext = $file[1];
	
		# Display the proper headers depending on filetype
		if ($ext == "pdf") 
		{
			Header("Content-type: application/pdf");
			Header("Content-Disposition: attatchment; filename=resume.pdf");
		}
		else if ($ext == "doc")
		{
			Header("Content-type: application/msword");
			Header("Content-Disposition: attatchment; filename=resume.doc");
		}
		Header("Pragma: public");
		
		# Read zipped resume file to the browser
		readgzfile($path);
	}
	
?>