<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$committee = new Committee($_GET['committee_id']);

$template = new Template();

$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));

$seats = new Block('committees/seats.inc');
$seats->committee = $committee;
$template->blocks[] = $seats;

$topicList = new Block('topics/topicList.inc');
$topicList->topicList = $committee->getTopics();
$topicList->committee = $committee;
$template->blocks[] = $topicList;

echo $template->render();
