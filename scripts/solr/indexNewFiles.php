<?php
/**
 * Update the SOLR index with new documents that have not been indexed
 *
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Application\Models\MeetingFilesTable;
use Web\Database;
use Web\Search\Solr;

$_SERVER['REQUEST_URI'] = __FILE__;

include '../../bootstrap.php';

$solr   = new Solr($SOLR['onboard']);
$table  = new MeetingFilesTable();
$files  = $table->find(['indexed'=>false]);
$total  = count($files);
$c      = 0;

$db     = Database::getConnection();
$update = $db->createStatement('update meetingFiles set indexed=CURRENT_TIMESTAMP where id=?');

$solr->setTimeout(20);

echo "Found $total\n";
foreach ($files as $f) {
    $c++;
    echo "{$f->getFullPath()} {$f->getId()} $c/$total\n";
    $solr->add($f);
    $update->execute([$f->getId()]);
}
