<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
define('SITE_HOME', $_SERVER['SITE_HOME']);
include SITE_HOME.'/site_config.php';

$pdo    = db_connect($DATABASES['default']);

$tables = ['meetingFiles', 'legislationFiles', 'reports', 'applicantFiles'];
foreach ($tables as $table) {
    $query = $pdo->query("select * from $table");
    $files = $query->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($files as $f) {
        if (!is_file(SITE_HOME."/$table/".$f['internalFilename'])) {
            print_r($f);
        }
    }

    $query = $pdo->prepare("select * from $table where internalFilename=?");
    foreach (glob(SITE_HOME."/$table/*/*/*/*") as $f) {
        $internal = substr($f, -24);
        $query->execute([$internal]);
        $result   = $query->fetchAll(\PDO::FETCH_ASSOC);
        if (!$result) {
            echo "$f\n";
        }
    }
}

function db_connect(array $config): \PDO {
    $pdo = new \PDO($config['dsn'], $config['username'], $config['password']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
