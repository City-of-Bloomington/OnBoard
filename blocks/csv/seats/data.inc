<?php
/**
 * @copyright 2016-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array          $this->params    Parameters used to generate data
 * @param Laminas\Db\Result $this->data      Data structure returned as SeatTable::$dataFields
 * @param Committee      $this->committee (optional)
 * @param string         $this->title     (optional)
 */
use Application\Models\Person;

$date = isset($this->params['current'])
    ? $this->params['current']->format('Ymd')
    : date('Ymd');

$filename = APPLICATION_NAME.'-';
$filename.= $this->title ? $this->title : $this->_(['seat', 'seats', count($this->data['results'])]);
$filename.= "-$date";

header("Content-Disposition: attachment; filename=\"$filename.csv\"");

$userCanViewContactInfo = Person::isAllowed('people', 'viewContactInfo');
$fields = [
    'Committee','Seat Code', 'Seat Name','Appointer','Firstname','Lastname',
    'Term Start','Term End','Appointment Start', 'Appointment End'
];
if ($userCanViewContactInfo) {
    $fields[] = 'Email';
    $fields[] = 'Address';
    $fields[] = 'City';
    $fields[] = 'State';
    $fields[] = 'Zip';
}
echo implode(',', $fields)."\n";

$out = fopen('php://output', 'w');

foreach ($this->data['results'] as $r) {
    $row = [
        $r['committee_name'],
        $r['seat_code'],
        $r['seat_name'],
        $r['appointer_name'],
        $r['member_firstname'],
        $r['member_lastname' ],
        $r['member_termStart'],
        $r['member_termEnd'  ],
        $r['member_startDate'],
        $r['member_endDate'  ]
    ];
    if ($userCanViewContactInfo) {
        $row[] = $r['member_email'  ];
        $row[] = $r['member_address'];
        $row[] = $r['member_city'   ];
        $row[] = $r['member_state'  ];
        $row[] = $r['member_zip'    ];
    }
    fputcsv($out, $row);
}
fclose($out);
