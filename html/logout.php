<?php
/*
 	Logs a user out of the system
*/
	session_destroy();

	Header("Location: ".BASE_URL);
?>