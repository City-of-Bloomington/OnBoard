<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', realpath(__DIR__.'/../../'));
define('VERSION', trim(file_get_contents(APPLICATION_HOME.'/VERSION')));

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

/**
 * Enable autoloading for the PHP libraries
 */
$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Site\\', SITE_HOME);

include SITE_HOME.'/site_config.php';
include APPLICATION_HOME.'/src/Web/routes.php';
include APPLICATION_HOME.'/src/Web/access_control.php';

/**
 * Graylog is a centralized log manager
 *
 * This application supports sending errors and exceptions to a graylog instance.
 * This is handy for notifying developers of a problem before users notice.
 * @see https://graylog.org
 */
if (defined('GRAYLOG_DOMAIN') && defined('GRAYLOG_PORT')) {
    $graylog = new Web\GraylogWriter(GRAYLOG_DOMAIN, GRAYLOG_PORT);
    $logger  = new Laminas\Log\Logger();
    $logger->addWriter($graylog);
    Laminas\Log\Logger::registerErrorHandler($logger);
    Laminas\Log\Logger::registerExceptionHandler($logger);
    Laminas\Log\Logger::registerFatalErrorShutdownFunction($logger);
}