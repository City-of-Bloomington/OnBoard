<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;
use Web\Database;

include '../../src/Web/bootstrap.php';

$table = new CommitteeTable();
$list  = $table->find(['id'=>79]);
foreach ($list as $c) {
    print_r($c);
    $c->syncGoogleCalendar();
}
