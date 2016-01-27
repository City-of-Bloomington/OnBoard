<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
use Blossom\Classes\Block;
?>
<header class="fn1-uiBlock-header">
    <h1 class="fn1-uiBlock-title"><?= sprintf($this->_('reappointToSeat', 'messages'), $this->newMember->getPerson()->getFullName() , $this->newMember->getCommittee()->getName()); ?></h1>
</header>
