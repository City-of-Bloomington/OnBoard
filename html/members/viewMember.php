<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET member_id
 */
$member = new Member($_GET['member_id']);

$template = new Template();

$personalInfo = new Block('users/personalInfo.inc');
$personalInfo->user = $member->getUser();
$template->blocks[] = $personalInfo;

$seats = new Block('members/seatList.inc');
$seats->memberList = $member->getUser()->getMembers();
$template->blocks[] = $seats;

$votingRecord = new Block('members/votingRecord.inc');
$votingRecord->user = $member->getUser();
$template->blocks[] = $votingRecord;

echo $template->render();
