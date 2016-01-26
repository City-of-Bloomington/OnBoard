<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->currentMember
 */
$picker = $this->template->getHelper('datePicker');
?>
<input name="currentMember[member_id]" type="hidden" value="<?= $this->currentMember->getId(); ?>" />
<dl class="fn1-input-field">
    <dt><label  for="currentMember-endDate"><?= $this->_('endDate'); ?></label></dt>
    <dd><?php
            echo $picker->datePicker(
                'currentMember[endDate]',
                'currentMember-endDate',
                $this->currentMember->getEndDate('U')
            );
        ?>
    </dd>
</dl>
