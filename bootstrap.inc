<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', __DIR__);
define('BLOSSOM', APPLICATION_HOME.'/vendor/City-of-Bloomington/blossom-lib');

// Path to LibreOffice for converting files to PDF
define('SOFFICE', '/usr/bin/soffice');

/**
 * Multi-Site support
 *
 * To allow multiple sites to use this same install base,
 * define the SITE_HOME variable in the Apache config for each
 * site you want to host.
 *
 * SITE_HOME is the directory where all site-specific data and
 * configuration are stored.  For backup purposes, backing up this
 * directory would be sufficient for an easy full restore.
 */
define('SITE_HOME', !empty($_SERVER['SITE_HOME']) ? $_SERVER['SITE_HOME'] : __DIR__.'/data');
include SITE_HOME.'/site_config.inc';

//-------------------------------------------------------------------
// Bootstrap code
// No editing is usually needed after this point
//-------------------------------------------------------------------
/**
 * Enable autoloading for the PHP libraries
 */
$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Site\\', SITE_HOME);

#include APPLICATION_HOME.'/routes.inc';
include APPLICATION_HOME.'/access_control.inc';

/**
 * Session Startup
 * Don't start a session for CLI usage.
 * We only want sessions when PHP code is executed from the webserver
 */
if (!defined('STDIN')) {
	ini_set('session.save_path', SITE_HOME.'/sessions');
	ini_set('session.cookie_path', BASE_URI);
	session_start();
}

/**
 * Skidder is a web service for error loggin.  This application supports
 * logging errors to a Skidder server.  You must register for an application_id
 * on the skidder server you want to post errors to.
 */
#$writer = new Blossom\Classes\Log\SkidderWriter('https://apps.bloomington.in.gov/skidder/index', 3 );
#$logger = new Zend\Log\Logger();
#$logger->addWriter($writer);
#Zend\Log\Logger::registerErrorHandler($logger);
#Zend\Log\Logger::registerExceptionHandler($logger);

/**
 * Grab a timestamp for calculating process time
 */
$startTime = microtime(1);
