<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
$committeeName = View::escape($this->seat->getCommittee()->getName());
$seatName = View::escape($this->seat->getName());
echo "
<h1><a href=\"{$this->seat->getCommittee()->getURL()}\">$committeeName</a></h1>
<h2><a href=\"{$this->seat->getURL()}\">$seatName</a></h2>
";
