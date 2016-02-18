<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\CommitteeTable;
use Blossom\Classes\Url;

$table = new CommitteeTable();
$list  = $table->find();

$filename = APPLICATION_NAME.'-liasons-'.date('Ymd');
header("Content-Disposition: attachment; filename=\"$filename.csv\"");
echo "Committee ID,Committee,Departments,Liason ID,Name,Email,Phone\n";

$out = fopen('php://output', 'w');
foreach ($list as $committee) {

    $departments = [];
    foreach ($committee->getDepartments() as $department) {
        $departments[] = $department->getName();
    }
    $departments = implode('|', $departments);

    foreach ($committee->getLiasonPeople() as $person) {
        $row = [
            $committee->getId(),
            $committee->getName(),
            $departments,
            $person->getId(),
            $person->getFullname(),
            $person->getEmail(),
            $person->getPhone()
        ];
        fputcsv($out, $row);
    }
}
fclose($out);