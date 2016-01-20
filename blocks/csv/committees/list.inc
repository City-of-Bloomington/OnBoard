<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\Database;

$sql = "select  c.name as committee,
                s.name as seat,
                a.name as appointer,
                p.firstname, p.lastname,
                t.startDate, t.endDate
        from members m
        join terms t on m.term_id=t.id
        join seats s on m.seat_id=s.id
        join appointers a on s.appointer_id=a.id
        join committees c on m.committee_id=c.id
        join people p on m.person_id=p.id
        order by c.name, s.name";
$zend_db = Database::getConnection();
$result = $zend_db->query($sql)->execute();

header('Content-Disposition: attachment; filename="committees.csv"');
echo "Committee,Seat,Appointer,Firstname,Lastname,Term Start,Term End\n";

$out = fopen('php://output', 'w');
foreach ($result as $row) {
    fputcsv($out, $row);
}
fclose($out);