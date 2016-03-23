<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;

$userCanRemove = !$this->disableButtons && Person::isAllowed('liaisons', 'remove');
$removeButton  = '';
$helper        = $this->template->getHelper('buttonLink');

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

    if ($userCanRemove) {
        $removeButton = $helper->buttonLink(
            BASE_URI."/liaisons/remove?committee_id=$committee_id;person_id=$person_id",
            $this->_('liaison_remove'),
            'delete'
        );
        $removeButton = "<td>$removeButton</td>";
    }

    echo "
    <tr>$committeeTD
        <td>$department</td>
        <td>{$this->_($type)}</td>
        $personTD
        $removeButton
    </tr>
    ";
}
echo "</table>";
