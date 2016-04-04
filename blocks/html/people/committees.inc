<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\LiaisonTable;

$members = $this->person->getMembers();
?>
<section>
    <h1><?= $this->_(['membership', 'memberships', count($members)]); ?></h1>
    <table>
    <?php
    $committees = [];
    foreach ($members as $member) {
        $committee      = $member->getCommittee();
        $committee_uri  = BASE_URI.'/committees/members?committee_id='.$committee->getId();
        $committee_name = self::escape($committee->getName());

        $offices = $this->person->getOffices($committee);
        if (count($offices)) {
            $o = $offices[0];
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
$this->data = LiaisonTable::personLiaisonData(['person_id'=>$this->person->getId()]);
if (count($this->data)) {
    $l = $this->_(['liaisonship', 'liaisonships', count($this->data)]);
    echo "<section><h1>$l</h1>";
    $this->_include('liaisons/partials/dataTable.inc');
    echo '</section>';
}
?>