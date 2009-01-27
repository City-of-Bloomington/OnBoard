<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET committee_id
 */
$committee = new Committee($_GET['committee_id']);

$format = isset($_GET['format']) ? $_GET['format'] : 'html';
$template = new Template('default',$format);
$template->title = $committee->getName();

if ($template->outputFormat == 'html') {
	$template->blocks[] = new Block('committees/breadcrumbs.inc',array('committee'=>$committee));
}

$template->blocks[] = new Block('committees/committeeInfo.inc',array('committee'=>$committee));

if ($template->outputFormat == 'html') {
	$seats = new Block('committees/seats.inc');
	$seats->committee = $committee;
	$template->blocks[] = $seats;

	$tagCloud = new Block('topics/tagCloud.inc');
	$tagCloud->topicList = $committee->getTopics();
	$template->blocks[] = $tagCloud;

	$votingComparison = new Block('topics/votingRecordComparison.inc');
	$votingComparison->topicList = $committee->getTopics();
	$template->blocks[] = $votingComparison;

	$topics = new Block('topics/topicList.inc');
	$topics->topicList = $committee->getTopics();
	$topics->committee = $committee;
	$template->blocks[] = $topics;
}

echo $template->render();
