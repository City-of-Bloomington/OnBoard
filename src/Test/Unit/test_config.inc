<?php
define('APPLICATION_NAME','onboard');

/**
 * URL Generation settings
 *
 * Do NOT use trailing slashes
 *
 * If your site is being proxied, change BASE_HOST to the hostname
 * used for the outside world.
 */
define('BASE_URI' , '/onboard');
#define('BASE_HOST', isset($_SERVER['HTTP_X_FORWARDED_HOST'])
#                        ? $_SERVER['HTTP_X_FORWARDED_HOST']
#                        : $_SERVER['SERVER_NAME']);
define('BASE_HOST', 'localhost');
define('BASE_URL' , "https://".BASE_HOST.BASE_URI);

define('RECAPTCHA_SITE_KEY',   '');
define('RECAPTCHA_SERVER_KEY', '');
define('GOOGLE_CREDENTIALS_FILE', __DIR__.'/credentials.json');
define('GOOGLE_USER_EMAIL', 'user@localhost');
define('GOOGLE_CALENDAR_URL', 'https://calendar.google.com');

/**
 * Specify the theme directory
 *
  * Remember to create a symbolic link in public/css to the theme CSS
  * that you declare here.
 *
 * A theme can consist of templates, blocks which will override core.
 * The current theme's screen.css will be included in the HTML head.
 */
define('THEME', 'Kirkwood');

/**
 * Database Setup
 * Refer to the PDO documentation for DSN sytnax for your database type
 * http://www.php.net/manual/en/pdo.drivers.php
 */
$DATABASES = [
    'default' => [
        'driver'   => 'Pdo_Mysql',
        'dsn'      => 'mysql:dbname=onboard;host=localhost',
        'username' => 'onboard',
        'password' => 'something secret',
        'options'  => []
    ]
];

/**
 * Directory Configuration
 *
 * This supports doing user authentication from multiple external
 * directories, such as LDAP or ADS. This is required since city staff
 * are in a seperate LDAP directory from public user accounts.
 * Classes that implement ExternalIdentity should have an entry here.
 *
 * See: ExternalIdentity
 */
$LDAP = [
// Example for ADS style authentication
//  'Ldap' => [
//      'classname' => 'Site\Classes\Ldap',
//      'DIRECTORY_SERVER'=>'ldaps://example.org:636',
//      'DIRECTORY_BASE_DN'=>'OU=Department,DC=example,DC=org',
//      'DIRECTORY_USERNAME_ATTRIBUTE'=>'CN',
//      'DIRECTORY_USER_BINDING'=>'{username}@example.org',
//      'DIRECTORY_ADMIN_BINDING'=>'admin@example.org',
//      'DIRECTORY_ADMIN_PASS'=>'password'
//  ]
//
// Example for LDAP style authentication
//  'Ldap' => [
//      'classname' => 'Site\Classes\Ldap',
//      'DIRECTORY_SERVER'=>'ldaps://example.org:636');
//      'DIRECTORY_BASE_DN'=>'ou=people,o=ldap.domain.somewhere');
//      'DIRECTORY_USERNAME_ATTRIBUTE'=>'uid');
//      'DIRECTORY_USER_BINDING'=>'uid={username},'.DIRECTORY_BASE_DN);
//      'DIRECTORY_ADMIN_BINDING'=>'uid=admin,'.DIRECTORY_BASE_DN);
//      'DIRECTORY_ADMIN_PASS'=>'password');
//  ]
// Example for Directory webservice
//  'Employee' => [
//      'classname' => 'Site\Classes\Employee',
//      'DIRECTORY_SERVER' => 'http://localhost/directory',
//  ]
];

/**
 * CAS authentication http://www.jasig.org/cas
 *
 * https://wiki.jasig.org/display/CASC/phpCAS
 *
 * phpCAS is a PHP library for handling the calls to the CAS service
 * It is the official library, part of the Jasig CAS project
 */
//define('CAS', APPLICATION_HOME.'/vendor/jasig/phpcas/source');
//define('CAS_SERVER','cas.somewhere.org');
//define('CAS_URI','cas');

define('DATE_FORMAT', 'n/j/Y');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'n/j/Y H:i:s');

define('LOCALE', 'en_US');

define('TERM_END_WARNING_DEFAULT', 60);
define('APPLICATION_LIFETIME_DEFAULT', 90);
define('ADMINISTRATOR_EMAIL', 'someone@localhost');

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
