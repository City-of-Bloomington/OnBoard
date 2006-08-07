<?php
/*
	Gets a user's photo out of LDAP and streams it to the browser as an image

	$_GET variables:	user
*/
	$ldap = new LDAPEntry($_GET['user']);
	
	Header('Content-type: image/jpeg');
	print_r($ldap->getPhoto());
?>