<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;

$actions       = [];
$userCanEdit   = !$this->disableButtons && Person::isAllowed('liaisons', 'edit');
$userCanDelete = !$this->disableButtons && Person::isAllowed('liaisons', 'delete');

$committeeTD = '';
$personTD    = '';

echo "<table class=\"fn1-table\">";
foreach ($this->data['results'] as $row) {
    // Create local, escaped variables for all the fields
    // See: Committee::liasonData()
    foreach ($row as $f=>$v) { $$f = self::escape($v); }

    if (!isset($this->committee)) {
        $committeeUri  = BASE_URI."/committees/info?committee_id=$committee_id";
        $committeeTD   = "<td><a href=\"$committeeUri\">$committee</a></td>";
    }
    if (!isset($this->person)) {
        $personUri     = BASE_URI."/people/view?person_id=$person_id";
        $personTD = "
        <td><a href=\"$personUri\">$firstname $lastname</a></td>
        <td>$email</td>
        <td>$phone</td>
        ";
    }

    if ($userCanEdit)   { $actions[] = ['url'=>BASE_URI."/liaisons/update?liaison_id=$liaison_id", 'label'=>$this->_('liaison_edit'  )]; }
    if ($userCanDelete) { $actions[] = ['url'=>BASE_URI."/liaisons/delete?liaison_id=$liaison_id", 'label'=>$this->_('liaison_delete')]; }
    if (count($actions)) {
        $h = $this->template->getHelper('dropdown');
        $moreLinks = '<td>'.$h->dropdown($actions, $this->_('more'), 'fn1-table-actions').'</td>';
    }

    echo "
    <tr>$committeeTD
        <td>$department</td>
        <td>{$this->_($type)}</td>
        $personTD
        $moreLinks
    </tr>
    ";
}
echo "</table>";
