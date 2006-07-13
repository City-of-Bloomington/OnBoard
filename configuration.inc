<?php
/*
	Global settings
*/
	define("APPLICATION_NAME","committee_manager");	# Used to keep sessions on the same webserver seperate;

	define("GLOBAL_INCLUDES","/var/www/libraries/test");	# Where on the filesystem this package of libraries is installed.
														# These are shared among other applications we write, so they
														# are a seperate package we will release.

	define("APPLICATION_HOME","/var/www/sites/committee_manager");	# Where on the filesystem this is installed
	define("BASE_URL","/committee_manager");							# The base URL to get to this site on this webserver

	define("FCK_EDITOR", "/var/www/sites/FCKeditor");    # Location of the FCKeditor

	#--------------------------------------------------------------------------
	# Database Setup
	#--------------------------------------------------------------------------
	try { $PDO = new PDO("mysql:unix_socket=/tmp/mysql.sock;dbname=committee_manager","committee","h++pd",array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)); }
	catch (PDOException $e) { die($e->getMessage()); }

	#--------------------------------------------------------------------------
	# LDAP Configuration
	# This is required in order to use the LDAP authentication
	# If you do not want to use LDAP authentication, you can comment this out
	#--------------------------------------------------------------------------
	define("LDAP_DOMAIN","city.bloomington.in.us");
	define("LDAP_DN","ou=people,o=".LDAP_DOMAIN);
	define("LDAP_USERNAME_ATTRIBUTE","uid");
	define("LDAP_ADMIN_USER","username");
	define("LDAP_ADMIN_PASS","password");
	define("LDAP_SERVER","ldap.bloomington.in.gov");
	define("LDAP_PASSWORD_ATTRIBUTE","userpassword");


	# Import global functions that we use for many applications we write
	include(GLOBAL_INCLUDES."/ITSFunctions.inc");

	#--------------------------------------------------------------------------
	# Session Startup
	#--------------------------------------------------------------------------
	session_start();
?>