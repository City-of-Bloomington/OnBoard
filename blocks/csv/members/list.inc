<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 *
 * @param Committee $this->committee      (optional)
 * @param Seat      $this->seat           (optional)
 * @param string    $this->title          (optional)
 * @param boolean   $this->disableButtons (optional)
 */
use Application\Models\Person;

$DATE_FORMAT = 'Y-m-d';

// Display current committee members
// These members will not be associated with a seat
$userCanViewContactInfo = Person::isAllowed('people', 'viewContactInfo');
// We need to match the output of /seats/data.inc CSV
$fields = [
    'Committee','Seat','Appointer','Firstname','Lastname',
    'Term Start','Term End','Appointment Start', 'Appointment End'
];
if ($userCanViewContactInfo) { $fields[] = 'Email'; }
echo implode(',', $fields)."\n";

$out = fopen('php://output', 'w');

$committee = $this->committee ? $this->committee : null;
foreach ($this->members as $m) {
    if (!$committee) { $committee = $m->getCommittee(); }
    $person = $m->getPerson();

    $row = [
        $committee->getName(),
        null,
        null,
        $person->getFirstname(),
        $person->getLastname(),
        null,
        null,
        $m->getStartDate($DATE_FORMAT),
        $m->getEndDate  ($DATE_FORMAT)
    ];
    if ($userCanViewContactInfo) { $row[] = $person->getEmail(); }
    fputcsv($out, $row);
}
fclose($out);
