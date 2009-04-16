<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET topic_id
 */
$topic = new Topic($_GET['topic_id']);

$template = new Template();
$template->title = $topic->getDescription();

$template->blocks[] = new Block('committees/committeeInfo.inc',
								array('committee'=>$topic->getCommittee()));

if ($template->outputFormat == 'html') {
	$template->blocks[] = new Block('topics/tabs.inc',
								array('topic'=>$topic,'currentTab'=>'legislation'));
	$template->blocks[] = new Block('topics/topicInfo.inc',array('topic'=>$topic));
}


echo $template->render();
