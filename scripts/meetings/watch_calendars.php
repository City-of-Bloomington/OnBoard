<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Models\CommitteeTable;
use Application\Models\GoogleGateway;

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
echo "
Usage: php $argv[0] SITE_HOME MINUTES
Register a Google Calendar watch for push notifications

SITE_HOME is the path to the OnBoard data directory.
MINUTES is the duration of the watch.

With the --help, -help, -h, or -? options, you can get this help.
";
exit();
}

$_SERVER['SITE_HOME'] = $argv[1];
$minutes = (int)$argv[2];

if (!is_file($_SERVER['SITE_HOME'].'/site_config.php')) {
echo "
php $argv[0]: invalid SITE_HOME -- '$argv[1]'
php $argv[0] --help' for more information
";
exit(1);
}

if (!($minutes > 1)) {
echo "
php $argv[0]: invalid MINUTES -- '$argv[2]'
php $argv[0] --help' for more information
";
exit(1);
}

include '../../src/Web/bootstrap.php';
$table   = new CommitteeTable();
$list    = $table->find(['current'=>true]);
$expires = strtotime("+$minutes minute");
$debug   = fopen(DEBUG_LOG, 'a');
fwrite($debug, "----------------\nwatch_calendars\n----------------\n");
foreach ($list['rows'] as $c) {
    $calendar_id = $c->getCalendarId();
    if ($calendar_id) {
        $watch_id = APPLICATION_NAME."-{$c->getId()}-".uniqid();
        $channel  = GoogleGateway::watch($calendar_id, $watch_id, $expires);
        fwrite($debug, "$watch_id\n");
        fwrite($debug, print_r($channel, true));
    }
}
