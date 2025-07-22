<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
use Web\Database;
use Application\Models\Applicant;
use Application\Models\ApplicantFilesTable;

include '../../../src/Web/bootstrap.php';

$db  = Database::getConnection();
$pdo = $db->getDriver()->getConnection()->getResource();

$select = $pdo->prepare('select max(id) from applicants where email=?');

$sql = "select email from applicants
        group by email having count(*)>1
        order by email";
$qq  = $pdo->query($sql);
$res = $qq->fetchAll(\PDO::FETCH_COLUMN, 0);
foreach ($res as $email) {
    $select->execute([$email]);
    (int)$id = $select->fetchColumn();
    echo "$email:$id\n";

    merge_applicants($id, $email);
    purgeFiles($id);
}

function merge_applicants(int $id, string $email)
{
    global $pdo;

    echo "merge_applicants($id, $email)\n";
    $table  = new ApplicantTable();
    $target = new Applicant($id);
    echo "Target {$target->getId()}:{$target->getEmail()}\n";

    $query  = $pdo->prepare('select id from applicants where email=?');
    $query->execute([$email]);
    $result = $query->fetchAll(\PDO::FETCH_COLUMN);
    if (!count($result)) {
        echo "No applicants found\n";
        exit();
    }
    foreach ($result as $applicant_id) {
        if ($applicant_id != $target->getId()) {
            $a = new Applicant($applicant_id);
            echo "Merge {$a->getId()} into {$target->getId()}\n";
            $target->mergeFrom($a);
        }
    }
}

function purgeFiles(int $applicant_id)
{
    $table = new ApplicantFilesTable();
    $list  = $table->find(['applicant_id'=>$applicant_id], 'created desc');
    $c = 0;
    foreach ($list as $f) {
        $c++;
        if ($c == 1) { continue; }
        echo "Deleting {$f->getId()}:{$f->getFilename()}\n";

        $f->delete();
    }
}
