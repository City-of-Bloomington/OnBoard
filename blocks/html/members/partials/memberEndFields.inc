<?php
/**
 * @copyright 2016-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Member $this->currentMember
 */
$helper     = $this->template->getHelper('field');
$term       = $this->currentMember->getTerm();
$maxEndDate = $term ? $term->getEndDate() : 'now';
?>
<input name="currentMember[member_id]" type="hidden" value="<?= $this->currentMember->getId(); ?>" />
<?php
    echo $helper->field([
        'name'  => 'currentMember[endDate]',
        'id'    => 'currentMember-endDate',
        'label' => $this->_('endDate'),
        'value' => new \DateTime($maxEndDate),
        'type'  => 'date'
    ]);
?>
