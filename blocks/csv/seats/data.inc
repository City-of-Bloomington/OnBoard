<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param Committee      $this->committee (optional)
 * @param string         $this->title     (optional)
 */
$filename = APPLICATION_NAME.'-';
$filename.= $this->title ? $this->title : $this->_(['seat', 'seats', count($this->data['results'])]);
$filename.= '-'.date('Ymd');

header("Content-Disposition: attachment; filename=\"$filename.csv\"");
echo "Committee,Seat,Appointer,Firstname,Lastname,Term Start,Term End,Appointment Start, Appointment End\n";

$out = fopen('php://output', 'w');
$now = time();

foreach ($this->data['results'] as $r) {
    $row = [
        $r['committee'],
        $r['seat'],
        $r['appointer'],
        $r['firstname'],
        $r['lastname'],
        $r['memberTermStart'],
        $r['memberTermEnd'],
        $r['memberStart'],
        $r['memberEnd']
    ];
    fputcsv($out, $row);
}
fclose($out);
