<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
use Blossom\Classes\Block;

$this->allocation = $this->seat->getAllocation();
include APPLICATION_HOME.'/blocks/html/allocations/panel.inc';
include APPLICATION_HOME.'/blocks/html/seats/info.inc';

$block = new Block('terms/list.inc', [
    'terms' => $this->seat->getTerms(),
    'seat'  => $this->seat
]);
echo $block->render('html', $this->template);
