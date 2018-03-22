<?php
/**
 * @copyright 2016-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array          $this->params    Parameters used to generate data
 * @param Zend\Db\Result $this->data      Data structure returned as SeatTable::$dataFields
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

$userCanViewContactInfo = Person::isAllowed('people', 'viewConcatInfo');
$fields = [
    'Committee','Seat','Appointer','Firstname','Lastname',
    'Term Start','Term End','Appointment Start', 'Appointment End'
];
if ($userCanViewContactInfo) { $fields[] = 'Email'; }
echo implode(',', $fields)."\n";

$out = fopen('php://output', 'w');

foreach ($this->data['results'] as $r) {
    $row = [
        $r['committee_name'],
        $r['seat_name'],
        $r['appointer_name'],
        $r['person_firstname'],
        $r['person_lastname'],
        $r['member_termStart'],
        $r['member_termEnd'],
        $r['member_startDate'],
        $r['member_endDate']
    ];
    if ($userCanViewContactInfo) { $row[] = $r['person_email']; }
    fputcsv($out, $row);
}
fclose($out);
