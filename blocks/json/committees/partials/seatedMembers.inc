<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param array $this->seats
 * @param string $this->title (optional)
 * @param Committee $this->committee (optional)
 */
if ($this->committee) {
    echo '{"info":';
        include APPLICATION_HOME.'/blocks/json/committees/info.inc';
    echo ',';
}


$DATE_FORMAT = 'Y-m-d';
$seats = [];

$now = time();

foreach ($this->seats as $seat) {
    $appointer = $seat->getAppointer_id()
        ? $seat->getAppointer()->getName()
        : '';

    $row = [
        'name'          => $seat->getName(),
        'appointedBy'   => $appointer,
        'startDate'     => $seat->getStartDate($DATE_FORMAT),
        'endDate'       => $seat->getEndDate  ($DATE_FORMAT),
        'type'          => $seat->getType(),
        'vacant'        => $seat->hasVacancy(),
        'currentTerm'   => null,
        'currentMember' => null
    ];
    if (!$this->committee) { $row['committee'] = $seat->getCommittee()->getName(); }

    if ($seat->getType() === 'termed') {
        $term = $seat->getTerm($now);
        if ($term) {
            $row['currentTerm'] = [
                'startDate'=> $term->getStartDate($DATE_FORMAT),
                'endDate'  => $term->getEndDate($DATE_FORMAT)
            ];
        }
    }

    $m = $seat->getCurrentMember();
    if ($m) {
        $offices = [];
        foreach ($m->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d')) as $office) {
            $offices[] = $office->getTitle();
        }

        $member = [
            'name'     => $m->getPerson()->getFullname(),
            'email'    => $m->getPerson()->getEmail(),
            'startDate'=> $m->getStartDate($DATE_FORMAT),
            'endDate'  => $m->getEndDate  ($DATE_FORMAT),
            'termStartDate' => null,
            'termEndDate' => null,
            'offices'  => implode(', ', $offices)
        ];

        if ($seat->getType() === 'termed') {
            $term = $m->getTerm();
            $member['termStartDate'] = $term->getStartDate($DATE_FORMAT);
            $member['termEndDate'  ] = $term->getEndDate  ($DATE_FORMAT);
        }
        $row['currentMember'] = $member;
    }
    $seats[] = $row;
}

echo '"seats":'.json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);

if ($this->committee) {
    echo '}';
}
