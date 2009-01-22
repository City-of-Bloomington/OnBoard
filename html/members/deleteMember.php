<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET member_id
 */
verifyUser(array('Administrator','Clerk'));

$member = new Member($_GET['member_id']);
$seat = $member->getSeat();

$member->delete();

Header('Location: '.$seat->getURL());


