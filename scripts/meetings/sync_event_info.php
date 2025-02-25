<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;
use Web\Database;

include '../../src/Web/bootstrap.php';

$table = new CommitteeTable();
$list  = $table->find(['current'=>true]);
#$list  = $table->find(['id'=>3]);
foreach ($list as $c) {
    echo "Sync Committee: ".$c->getName()."\n";
    $c->syncGoogleCalendar();
}
