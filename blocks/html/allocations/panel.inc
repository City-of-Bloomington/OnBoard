<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Allocation $this->allocation
 */
use Blossom\Classes\Block;

$this->committee = $this->allocation->getCommittee();
include APPLICATION_HOME.'/blocks/html/committees/panel.inc';
include APPLICATION_HOME.'/blocks/html/allocations/info.inc';

$block = new Block('seats/list.inc', [
    'seats'      => $this->allocation->getSeats(),
    'allocation' => $this->allocation
]);
echo $block->render('html', $this->template);
