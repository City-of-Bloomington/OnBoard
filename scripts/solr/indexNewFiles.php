<?php
/**
 * Update the SOLR index with new documents that have not been indexed
 *
 * @copyright 2023-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Web\Database;
use Web\Search\Solr;

$_SERVER['REQUEST_URI'] = __FILE__;

include '../../src/Web/bootstrap.php';

$db     = Database::getConnection();
$solr   = new Solr($SOLR['onboard']);
$solr->setTimeout(20);

$types  = [
    'meetingFiles'     => 'Application\Models\MeetingFilesTable',
    'legislationFiles' => 'Application\Models\Legislation\LegislationFilesTable',
    'reports'          => 'Application\Models\Reports\ReportsTable'
];
foreach ($types as $tablename=>$classname) {
    $table  = new $classname();
    $result = $table->find(['indexed'=>false]);
    $total  = $result['total'];
    $c      = 0;

    $update = $db->createStatement("update $tablename set indexed=CURRENT_TIMESTAMP where id=?");

    echo "Found $total $tablename\n";
    foreach ($result['rows'] as $f) {
        $c++;
        echo "{$f->getFullPath()} {$f->getId()} $c/$total\n";
        $solr->add($f);
        $update->execute([$f->getId()]);
    }
}
