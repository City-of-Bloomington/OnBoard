<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data      Data structure returned as SeatTable::$dataFields
 * @param Committee      $this->committee (optional)
 * @param string         $this->title     (optional)
 */
use Application\Models\Person;

if ($this->committee) {
    echo '{"info":';
        include APPLICATION_HOME.'/blocks/json/committees/info.inc';
    echo ',';
}

$userCanViewContactInfo = Person::isAllowed('people', 'viewContactInfo');

$seats = [];
foreach ($this->data['results'] as $r) {
    $row = [
        'name'          => $r['seat_name'     ],
        'appointedBy'   => $r['appointer_name'],
        'startDate'     => $r['seat_startDate'],
        'endDate'       => $r['seat_endDate'  ],
        'type'          => $r['seat_type'     ],
        'voting'        => $r['seat_voting'   ] ? true : false,
        'vacant'        => $r['carryOver'     ] || !$r['member_id']
    ];

    if ($r['seat_type'] == 'termed') {
        $row['currentTerm'] = [
            'startDate' => $r['term_startDate'],
            'endDate'   => $r['term_endDate'  ]
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
            'firstname'     => $r['person_firstname'],
            'lastname'      => $r['person_lastname' ],
            'website'       => $r['person_website'  ],
            'startDate'     => $r['member_startDate'],
            'endDate'       => $r['member_endDate'  ],
            'termStartDate' => $r['member_termStart'],
            'termEndDate'   => $r['member_termEnd'  ],
            'offices'       => $offices
        ];
        if ($userCanViewContactInfo) { $row['currentMember']['email'] = $r['person_email']; }
    }
    $seats[] = $row;
}
echo '"seats":'.json_encode($seats, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT);

if ($this->committee) {
    echo '}';
}
