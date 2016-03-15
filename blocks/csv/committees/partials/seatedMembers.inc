<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->seats
 * @param string $this->title
 */
$filename = APPLICATION_NAME.'-';
$filename.= $this->title ? $this->title : $this->_(['seat', 'seats', count($this->seats)]);
$filename.= '-'.date('Ymd');

header("Content-Disposition: attachment; filename=\"$filename.csv\"");
echo "Committee,Seat,Appointer,Firstname,Lastname,Term Start,Term End,Appointment Start, Appointment End\n";

$out = fopen('php://output', 'w');
$now = time();
foreach ($this->seats as $seat) {
    $appointer   = $seat->getAppointer_id() ? self::escape($seat->getAppointer()->getName()) : '';
    $firstname   = '';
    $lastname    = '';
    $termStart   = '';
    $termEnd     = '';
    $memberStart = '';
    $memberEnd   = '';

    $term = null;
    $member = $seat->getMember($now);
    if ($member) {
        $person      = $member->getPerson();
        $firstname   = $person->getFirstname();
        $lastname    = $person->getLastname();
        $memberStart = $member->getStartDate(DATE_FORMAT);
        $memberEnd   = $member->getEndDate  (DATE_FORMAT);

        if ($seat->getType() === 'termed') {
            $term = $member->getTerm();
        }
    }

    if (!$term && $seat->getType() === 'termed') {
        $term = $seat->getTerm($now);
    }

    if ($term) {
        $termStart = $term->getStartDate(DATE_FORMAT);
        $termEnd   = $term->getEndDate  (DATE_FORMAT);
    }

    $row = [
        $seat->getCommittee()->getName(),
        $seat->getName(),
        $appointer,
        $firstname,
        $lastname,
        $termStart,
        $termEnd,
        $memberStart,
        $memberEnd
    ];

    fputcsv($out, $row);
}
fclose($out);
