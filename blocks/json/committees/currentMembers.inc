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

if ($this->committee->getType() === 'seated') {
    $seats = [];

    foreach ($this->committee->getSeats(time()) as $seat) {
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
                'offices'  => implode(', ', $offices)
            ];

            if ($seat->getType() === 'termed') {
                $term = $m->getTerm();
                $member['termStartDate'] = $term->getStartDate($DATE_FORMAT);
                $member['termEndDate'  ] = $term->getEndDate  ($DATE_FORMAT);
            }
        }

        $appointer = $seat->getAppointer_id()
            ? $seat->getAppointer()->getName()
            : '';


        $seats[] = [
            'name'          => $seat->getName(),
            'appointedBy'   => $appointer,
            'startDate'     => $seat->getStartDate($DATE_FORMAT),
            'endDate'       => $seat->getEndDate  ($DATE_FORMAT),
            'type'          => $seat->getType(),
            'currentMember' => $member
        ];
    }

    echo '"seats":'.json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);
}
else {
    echo '"currentMembers":[]';
}
echo '}';
