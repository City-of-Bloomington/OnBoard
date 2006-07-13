<?php
/*

	GET variable: id (application id)

*/

verifyUser("Administrator");

	if (isset($_GET['id'])) 
	{
		$application = new Application($_GET['id']);
		$path = $application->getResumePath();
		$file = basename($path);
		$file = explode(".", $file);
		$ext = $file[1];
	
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
		
		readgzfile($path);
	}
	
?>