<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Laminas\Db\Result $this->data
 * @param boolean        $this->disableButtons
 * @param Committee      $this->committee      (optional)
 */
use Application\Models\Person;

$userCanEdit   = !$this->disableButtons && Person::isAllowed('liaisons', 'edit');
$userCanDelete = !$this->disableButtons && Person::isAllowed('liaisons', 'delete');

$committeeTD = '';
$personTD    = '';

echo "<table>";
foreach ($this->data['results'] as $row) {
    // Create local, escaped variables for all the fields
    // See: LiaisonTable::committeeLiaisonData()
    foreach ($row as $f=>$v) { $$f = parent::escape($v); }

    if (!$this->committee) {
        $committeeUri  = parent::generateUri('committees.info')."?committee_id=$committee_id";
        $committeeTD   = "<td><a href=\"$committeeUri\">$committee</a></td>";
    }

    if (!$this->person) {
        $personUri     = parent::generateUri('people.view')."?person_id=$person_id";
        $personTD = "<td><a href=\"$personUri\">$firstname $lastname</a></td>";
        if (Person::isAllowed('people', 'viewContactInfo')) {
            $personTD.= "<td>$email</td><td>$phone</td>";
        }
    }

    $actions   = [];
    $moreLinks = '';
    if ($userCanEdit)   { $actions[] = ['url'=>parent::generateUri('liaisons.update')."?liaison_id=$liaison_id", 'label'=>$this->_('liaison_edit'  )]; }
    if ($userCanDelete) { $actions[] = ['url'=>parent::generateUri('liaisons.update')."?liaison_id=$liaison_id", 'label'=>$this->_('liaison_delete')]; }
    if (count($actions)) {
        $h = $this->template->getHelper('dropdown');
        $moreLinks = '<td>'.$h->dropdown($actions, $this->_('more'), "actions_$liaison_id").'</td>';
    }

    echo "
    <tr>$committeeTD
        <td>{$this->_($type)}</td>
        $personTD
        $moreLinks
    </tr>
    ";
}
echo "</table>";
