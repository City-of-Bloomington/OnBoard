<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$DATE_FORMAT = 'Y-m-d';

echo '{"info":';
include APPLICATION_HOME.'/blocks/json/committees/info.inc';
echo ',';

$seats = [];

$now = time();

foreach ($this->committee->getSeats($now) as $seat) {
    $appointer = $seat->getAppointer_id()
        ? $seat->getAppointer()->getName()
        : '';

    $row = [
        'name'          => $seat->getName(),
        'appointedBy'   => $appointer,
        'startDate'     => $seat->getStartDate($DATE_FORMAT),
        'endDate'       => $seat->getEndDate  ($DATE_FORMAT),
        'type'          => $seat->getType(),
        'vacant'        => $seat->hasVacancy($now),
        'currentTerm'   => null,
        'currentMember' => null
    ];

    if ($seat->getType() === 'termed') {
        $term = $seat->getTerm($now);
        if ($term) {
            $row['currentTerm'] = [
                'startDate'=> $term->getStartDate($DATE_FORMAT),
                'endDate'  => $term->getEndDate($DATE_FORMAT)
            ];
        }
    }

    $m = $seat->getMember();
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
echo '}';
