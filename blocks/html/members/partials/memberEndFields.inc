<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Member $this->currentMember
 */
$helper = $this->template->getHelper('field');
?>
<input name="currentMember[member_id]" type="hidden" value="<?= $this->currentMember->getId(); ?>" />
<?php
    echo $helper->field([
        'name'  => 'currentMember[endDate]',
        'id'    => 'currentMember-endDate',
        'label' => $this->_('endDate'),
        'value' => new \DateTime($this->currentMember->getEndDate()),
        'type'  => 'date'
    ]);
?>
