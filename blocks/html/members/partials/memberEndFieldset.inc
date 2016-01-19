<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->currentMember
 */
?>
<fieldset><legend><?= $this->_('endDate'); ?></legend>
    <input name="currentMember[member_id]" type="hidden" value="<?= $this->currentMember->getId(); ?>" />
    <dl class="fn1-input-field">
        <dt><label  for="currentMember-endDate"><?= $this->_('endDate'); ?></label></dt>
        <dd><input name="currentMember[endDate]" id="currentMember-endDate"
                value="<?= $this->currentMember->getEndDate(DATE_FORMAT); ?>" />
        </dd>
    </dl>
</fieldset>
