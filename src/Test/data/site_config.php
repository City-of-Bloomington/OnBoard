<?php
define('APPLICATION_NAME','onboard');

define('BASE_URI' , '/onboard');
define('BASE_HOST', 'localhost');
define('BASE_URL' , 'https://'.BASE_HOST.BASE_URI);
define('USWDS_URL', '/static/uswds/dist');

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

define('DATE_FORMAT', 'n/j/Y');
define('TIME_FORMAT', 'g:i a');
define('DATETIME_FORMAT', 'n/j/Y g:i a');

define('LOCALE', 'en_US');

define('DEFAULT_CITY',                'Bloomington');
define('DEFAULT_STATE',               'IN');
define('DEFAULT_TERM_END_WARNING',     60);
define('DEFAULT_APPLICATION_LIFETIME', 90);
define('ADMINISTRATOR_EMAIL', 'someone@example.org');
