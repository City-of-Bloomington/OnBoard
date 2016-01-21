<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
header('Content-Disposition: attachment; filename="vacancies.csv"');
echo "Committee,Seat,Appointer,Firstname,Lastname,Term Start,Term End,Appointment Start, Appointment End\n";

$out = fopen('php://output', 'w');
foreach ($this->data as $row) {
    // We had to add these to the query in order to support
    // the HTML version.  We don't want them in the CSV data
    unset($row['committee_id']);
    unset($row['seat_id']);
    
    fputcsv($out, $row);
}
fclose($out);