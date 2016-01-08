<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Seat $this->seat
 */
use Blossom\Classes\Block;

$this->committee = $this->seat->getCommittee();
include APPLICATION_HOME.'/blocks/html/committees/panel.inc';
include APPLICATION_HOME.'/blocks/html/seats/info.inc';

if ($this->seat->getType() === 'termed') {
    $block = new Block('terms/list.inc', [
        'terms' => $this->seat->getTerms(),
        'seat'  => $this->seat
    ]);
    echo $block->render('html', $this->template);
}
