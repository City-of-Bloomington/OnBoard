<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', realpath(__DIR__.'/../../'));
define('VERSION', trim(file_get_contents(APPLICATION_HOME.'/VERSION')));

/**
 * Configuration and Data directory
 *
 * SITE_HOME is the directory where all site-specific data and
 * configuration are stored.  For backup purposes, backing up this
 * directory would be sufficient for an easy full restore.
 */
define('SITE_HOME', !empty($_SERVER['SITE_HOME']) ? $_SERVER['SITE_HOME'] : APPLICATION_HOME.'/data');

$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Site\\', SITE_HOME);

include SITE_HOME.'/site_config.php';
include APPLICATION_HOME.'/src/Web/routes.php';
include APPLICATION_HOME.'/src/Web/access_control.php';

$locale = LOCALE.'.utf8';
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain('labels',   APPLICATION_HOME.'/language');
bindtextdomain('messages', APPLICATION_HOME.'/language');
bindtextdomain('errors',   APPLICATION_HOME.'/language');
textdomain('labels');

/**
 * Graylog is a centralized log manager
 *
 * This application supports sending errors and exceptions to a graylog instance.
 * This is handy for notifying developers of a problem before users notice.
 * @see https://graylog.org
 */
if (defined('GRAYLOG_DOMAIN') && defined('GRAYLOG_PORT')) {
    $graylog = new \Web\GraylogWriter(GRAYLOG_DOMAIN, GRAYLOG_PORT);
             set_error_handler([$graylog, 'error'    ]);
         set_exception_handler([$graylog, 'exception']);
    register_shutdown_function([$graylog, 'shutdown' ]);
}


$f = SITE_HOME.'/debug/'.date('Y-m-d').'.log';
if (!is_dir(dirname($f))) {
    mkdir(dirname($f), 0770);
}
define('DEBUG_LOG', $f);
