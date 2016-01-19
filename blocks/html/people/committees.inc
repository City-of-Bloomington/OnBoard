<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
?>
<table>
<?php
$committees = [];
foreach ($this->person->getMembers() as $member) {
    $committee      = $member->getCommittee();
    $committee_uri  = BASE_URI.'/committees/members?committee_id='.$committee->getId();
    $committee_name = self::escape($committee->getName());

    $offices = $this->person->getOffices($committee);
    if (count($offices)) {
        $o = $offices->current();
        $office = self::escape($o->getTitle());
        $office = "($office)";
    }
    else {
        $office = '';
    }

    echo "
    <tr><td><a href=\"$committee_uri\">$committee_name</a></td>
        <td>$office</td>
        <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
    </tr>
    ";

}
?>
</table>