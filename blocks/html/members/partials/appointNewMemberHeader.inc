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
    <h1 class="fn1-uiBlock-title"><?= sprintf($this->_('appointToSeat', 'messages'), $this->newMember->getCommittee()->getName()); ?></h1>
</header>
<?php
    if ($this->newMember->getSeat()) {
        $summaryBlock = new Block('seats/summary.inc', ['seat' => $this->newMember->getSeat()]);
        echo '<aside class="onboard-contextInfo">';
        echo $summaryBlock->render('html', $this->template);
        echo '</aside>';
    }
?>
