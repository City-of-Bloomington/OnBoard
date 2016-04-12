<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 *
 * @param Committee $this->committee      (optional)
 * @param Seat      $this->seat           (optional)
 * @param string    $this->title          (optional)
 * @param boolean   $this->disableButtons (optional)
 */
$DATE_FORMAT = 'Y-m-d';

if ($this->committee) {
    echo '{"info":';
        include APPLICATION_HOME.'/blocks/json/committees/info.inc';
    echo ',';
}
// Display current committee members
// These members will not be associated with a seat
$seats = [];
foreach ($this->members as $m) {
    $name = self::escape($m->getPerson()->getFullname());

    $offices = [];
    foreach ($m->getPerson()->getOffices($m->getCommittee(), date('Y-m-d')) as $office) {
        $offices[] = $office->getTitle();
    }

    $seats[] = [
        'name'          => null,
        'appointedBy'   => null,
        'startDate'     => null,
        'endDate'       => null,
        'type'          => null,
        'vacant'        => false,
        'currentTerm'   => null,
        'currentMember' => [
            'name'          => $m->getPerson()->getFullname(),
            'startDate'     => $m->getStartDate($DATE_FORMAT),
            'endDate'       => $m->getEndDate  ($DATE_FORMAT),
            'termStartDate' => null,
            'termEndDate'   => null,
            'offices'       => implode(', ', $offices)
        ]
    ];
}
echo '"seats":'.json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);

if ($this->committee) {
    echo '}';
}
