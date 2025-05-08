<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;
use Web\Database;

if ($argc != 2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
echo "
Usage: php $argv[0] SITE_HOME
Updates committee meetings reading from their Google Calendars

SITE_HOME is the path to the OnBoard data directory.

With the --help, -help, -h, or -? options, you can get this help.
";
exit();
}

$_SERVER['SITE_HOME'] = $argv[1];

if (!is_file($_SERVER['SITE_HOME'].'/site_config.php')) {
echo "
php $argv[0]: invalid SITE_HOME -- '$argv[1]'
php $argv[0] --help' for more information
";
exit(1);
}

include '../../src/Web/bootstrap.php';
$table = new CommitteeTable();
$list  = $table->find(['current'=>true]);
foreach ($list as $c) {
    echo "Sync Committee: ".$c->getName()."\n";
    $c->syncGoogleCalendar();
}
