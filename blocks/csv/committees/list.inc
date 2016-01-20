<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\Database;

$sql = "select c.name as committee,
               s.name as seat,
               a.name as appointer,
               p.firstname, p.lastname,
               t.startDate as termStart,        t.endDate as termEnd,
               m.startDate as appointmentStart, m.endDate as appointmentEnd
        from committees c
        left join seats      s on c.id=s.committee_id
        left join appointers a on s.appointer_id=a.id
        left join members    m on (
            case when s.id is not null then s.id=m.seat_id
                 when c.type='open'    then c.id=m.committee_id
            end
            and m.startDate < now() and (m.endDate is null or now() < m.endDate)
        )
        left join terms      t on (
            case when m.id is not null then m.term_id=t.id
                 when m.id is null     then s.id=t.seat_id and t.startDate < now() and (t.endDate is null or now() < t.endDate)
            end
        )
        left join people  p on m.person_id=p.id
        order by c.name, s.name";
$zend_db = Database::getConnection();
$result = $zend_db->query($sql)->execute();

header('Content-Disposition: attachment; filename="committees.csv"');
echo "Committee,Seat,Appointer,Firstname,Lastname,Term Start,Term End,Appointment Start, Appointment End\n";

$out = fopen('php://output', 'w');
foreach ($result as $row) {
    fputcsv($out, $row);
}
fclose($out);