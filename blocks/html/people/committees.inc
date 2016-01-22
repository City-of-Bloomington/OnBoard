<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$members = $this->person->getMembers();
?>
<section>
    <h1><?= $this->_(['member', 'members', count($members)]); ?></h1>
    <table>
    <?php
    $committees = [];
    foreach ($members as $member) {
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
</section>

<?php
$committees = $this->person->getLiasonCommittees();
if (count($committees)) {
    $l = $this->_(['liason', 'liasons', count($committees)]);
    echo "<section><h1>$l</h1><table>";
    foreach ($committees as $c) {
        $name = self::escape($c->getName());
        $uri  = BASE_URI.'/committees/info?committee_id='.$c->getId();
        echo "<tr><td><a href=\"$uri\">$name</a></td></tr>";
    }
    echo '</table></section>';
}
?>