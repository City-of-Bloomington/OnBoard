<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
use Blossom\Classes\Block;
?>

<h1><?= sprintf($this->_('appointToSeat', 'messages'), $this->seat->getCommittee()->getName()) ?></h1>

<dl class="fn1-output-field">
    <dt><?= $this->_('appointed_by') ?></dt>
    <dd><?= $this->seat->getAppointer(); ?></dd>
</dl>

<dl class="fn1-output-field">
    <dt><?= $this->_(['requirement', 'requirements', 2]) ?></dt>
    <dd><?= $this->seat->getRequirements(); ?></dd>
</dl>
