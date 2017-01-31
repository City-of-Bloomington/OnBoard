<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
        'value' => $this->currentMember->getEndDate('U'),
        'type'  => 'date'
    ]);
?>
