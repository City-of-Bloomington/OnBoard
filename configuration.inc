<?php
/**
 * Replace this copyright statement with your own.  All the generated code
 * will include this copyright statement
 */
define('COPYRIGHT',"/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */");
/**
 * Used to keep sessions on the same webserver seperate;
 */
define('APPLICATION_NAME','committee_manager');

/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME','C:/www/sites/committee_manager');

/**
 * Where on the filesystem the framework is installed.
 */
define('FRAMEWORK',APPLICATION_HOME.'/libraries/framework');

/**
 * The URL to get to this site
 * Do NOT use a trailing slash
 */
define('BASE_URL','http://localhost:8080/committee_manager');

/**
 * Used when there's an error on the site.  The Framework will
 * print out a nice error message, encouraging users to report any problems
 * See: FRAMEWORK/ITSFunctions.inc
 */
define('ADMINISTRATOR_NAME','Site Admin');
define('ADMINISTRATOR_EMAIL','sibow@bloomington.in.gov');

/**
 * Set how we want to handle errors
 * PHP_DEFAULT - do whatever's configured in php.ini
 *
 * If you do not define error handling to PHP_DEFAULT
 * the custom error handlers kick in.  All of the custom error display
 * frunctions are in FRAMEWORK/globalFunctions.inc.  The custom error
 * function decide what to do based on $ERROR_REPORING array values
 *
 * PRETTY_PRINT - Display a message in the browser
 * EMAIL_ADMIN - email the Administrator
 * EMAIL_USER - email the logged in user
*/
define('ERROR_REPORTING','PHP_DEFAULT');
#define('ERROR_REPORTING','CUSTOM');
#$ERROR_REPORTING = array('EMAIL_ADMIN');

/**
 * Database Setup
 * Refer to the PDO documentation for DSN sytnax
 * for your database type
 * http://www.php.net/manual/en/pdo.drivers.php

 */

define('DB_TYPE','mysql');
define('DB_DSN','host=bandit.bloomington.in.gov;'); # You must include the trailing semi-colon 
define('DB_NAME','comit');
define('DB_USER','comit');
define('DB_PASS','h++pd');


/**
 * LDAP Configuration
 * This is required in order to use the LDAP authentication
 * If you do not want to use LDAP authentication, you can comment this out
 */

define('LDAP_DOMAIN','city.bloomington.in.us');
define('LDAP_DN','ou=people,o='.LDAP_DOMAIN);
define('LDAP_USERNAME_ATTRIBUTE','uid');
define('LDAP_ADMIN_USER','admin');
define('LDAP_ADMIN_PASS','lita98!');
define('LDAP_SERVER','ldap.bloomington.in.gov');
define('LDAP_PASSWORD_ATTRIBUTE','userpassword');


/**
 * Import global functions that we use for many applications we write
 */
include FRAMEWORK.'/globalFunctions.inc';

/**
 * Session Startup
 */
session_start();
