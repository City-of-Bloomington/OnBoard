<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET seat_id
 */
$seat = new Seat($_GET['seat_id']);

$template = new Template();
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$seat->getCommittee()));
$template->blocks[] = new Block('seats/seatInfo.inc',array('seat'=>$seat));

$members = new Block('members/memberList.inc');
$members->memberList = $seat->getMembers();
$members->seat = $seat;
$template->blocks[] = $members;

echo $template->render();