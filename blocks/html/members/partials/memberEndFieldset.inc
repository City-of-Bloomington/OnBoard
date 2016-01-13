<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->currentMember
 */
?>
<fieldset><legend><?= $this->_('previous_member'); ?></legend>
    <input name="currentMember[member_id]" type="hidden" value="<?= $this->currentMember->getId(); ?>" />
    <table>
        <tr><td><label  for="currentMember-endDate"><?= $this->_('endDate'); ?></label></td>
            <td><input name="currentMember[endDate]" id="currentMember-endDate"
                    value="<?= $this->currentMember->getEndDate(DATE_FORMAT); ?>" />
            </td>
        </tr>
    </table>
</fieldset>
