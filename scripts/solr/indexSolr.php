<?php
/**
 * Clear and reindex onboard data in a Solr core
 *
 * @copyright 2021 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

use Application\Models\MeetingFilesTable;
use Web\Search\Solr;

include '../../bootstrap.php';

$solr   = new Solr($SOLR['onboard']);
$solr->purge();
$client = $solr->getClient();
$client->getAdapter()->setTimeout(20);
$buffer = $client->getPlugin('bufferedadd');
$buffer->setBufferSize(100);

$table = new MeetingFilesTable();
$files = $table->find();
$total = count($files);
$c     = 0;
foreach ($files as $f) {
    $c++;
    echo "{$f->getFullPath()} $c/$total\n";
    $data = $solr->prepareIndexFields($f);
    $buffer->createDocument($data);
}
$buffer->commit();

$update = $client->createUpdate();
$update->addOptimize();
$client->update($update);
