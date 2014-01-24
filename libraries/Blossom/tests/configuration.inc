<?php
/**
 * This is the minimal configuration needed for unit testing.
 * No database or external service configuration is included
 */
/**
 * Displayed in the banner
 */
define('APPLICATION_NAME','application');

/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME','/home/inghamn/Desktop/Projects/Blossom/blossom');
define('BLOSSOM', APPLICATION_HOME.'/libraries/Blossom');
define('ZEND',    APPLICATION_HOME.'/libraries/zf2/library/Zend');

/**
 * Set how we want to handle errors
 * PHP_DEFAULT - do whatever's configured in php.ini
 *
 * If you do not define error handling to PHP_DEFAULT
 * the custom error handlers kick in.  All of the custom error display
 * frunctions are in BLOSSOM/Classes/Error.php.  The custom error
 * function decide what to do based on $ERROR_REPORING array values
 *
 * PRETTY_PRINT - Display a message in the browser
 * EMAIL_ADMIN - email the Administrator
 * EMAIL_USER - email the logged in user
 * SKIDDER - post errors to a Skidder server (see config below)
*/
define('ERROR_REPORTING','PHP_DEFAULT');
//define('ERROR_REPORTING','CUSTOM');
//$ERROR_REPORTING = array('PRETTY_PRINT','SKIDDER');

//-------------------------------------------------------------------
// Bootstrap code
// No editing is usually needed after this point
//-------------------------------------------------------------------
/**
 * Enable autoloading for the PHP libraries
 */
require_once ZEND.'/Loader/AutoloaderFactory.php';
$config = [
	'Zend\Loader\StandardAutoloader' => [
		'namespaces' => [
			'Application' => APPLICATION_HOME.'/libraries/Application',
			'Blossom'     => BLOSSOM,
			'Zend'        => ZEND
		]
	]
];
Zend\Loader\AutoloaderFactory::factory($config);

/**
 * Session Startup
 * Don't start a session for CLI usage.
 * We only want sessions when PHP code is executed from the webserver
 */
if (!defined('STDIN')) {
	ini_set('session.save_path',APPLICATION_HOME.'/data/sessions');
	session_start();
}

if (ERROR_REPORTING != 'PHP_DEFAULT') {
	set_error_handler    ('Blossom\Classes\Error::customErrorHandler');
	set_exception_handler('Blossom\Classes\Error::customExceptionHandler');
}

/**
 * Load the Zend_Acl
 */
include APPLICATION_HOME.'/access_control.inc';

/**
 * Grab a timestamp for calculating process time
 */
$startTime = microtime(1);
