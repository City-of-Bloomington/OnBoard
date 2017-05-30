<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param Committee      $this->committee (optional)
 * @param string         $this->title     (optional)
 */
if ($this->committee) {
    echo '{"info":';
        include APPLICATION_HOME.'/blocks/json/committees/info.inc';
    echo ',';
}

$seats = [];
foreach ($this->data['results'] as $r) {
    $row = [
        'committee'     => $r['committee'],
        'name'          => $r['seat'],
        'appointedBy'   => $r['appointer'],
        'startDate'     => $r['seatStart'],
        'endDate'       => $r['seatEnd'],
        'type'          => $r['seatType'],
        'vacant'        => $r['carryOver'] || !$r['member_id']
    ];

    if ($r['seatType'] === 'termed') {
        $row['currentTerm'] = [
            'startDate' => $r['termStart'],
            'endDate'   => $r['termEnd']
        ];
    }

    if ($r['member_id']) {
        $offices = '';
        if ($r['offices']) {
            $temp = [];
            foreach (explode(',', $r['offices']) as $o) {
                list($id, $title) = explode('|', $o);
                $temp[] = $title;
            }
            $offices = implode(',', $temp);
        }

        $row['currentMember'] = [
            'firstname'     => $r['firstname'],
            'lastname'      => $r['lastname'],
            'email'         => $r['email'],
            'startDate'     => $r['memberStart'],
            'endDate'       => $r['memberEnd'],
            'termStartDate' => $r['memberTermStart'],
            'termEndDate'   => $r['memberTermEnd'],
            'offices'       => $offices
        ];
    }
    $seats[] = $row;
}
echo '"seats":'.json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);

if ($this->committee) {
    echo '}';
}
