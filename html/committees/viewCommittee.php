<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);

$committee = new Committee($_GET['committee_id']);
$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));

if ($template->outputFormat == 'html')
{
	$seats = new Block('committees/seats.inc');
	$seats->committee = $committee;
	$template->blocks[] = $seats;

	$topics = new Block('topics/topicList.inc');
	$topics->topicList = $committee->getTopics();
	$template->blocks[] = $topics;
}

echo $template->render();
