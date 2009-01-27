<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param GET tag_id (optional)
 */
$topicList = new TopicList();


if (!isset($_GET['tag_id'])) {
	$topicList->find();
}
else {
	try {
		$tag = new Tag($_GET['tag_id']);
		$topicList->find(array('tag'=>$tag));
	}
	catch (Exception $e) {
		$topicList->find();
	}
}


$template = new Template();
if (isset($tag)) {
	$template->title = $tag->getName();
}
$template->title.= ' Legislation';

$breadcrumbs = new Block('topics/breadcrumbs.inc');
if (isset($tag)) {
	$breadcrumbs->tag = $tag;
}
$template->blocks[] = $breadcrumbs;

$votingComparison = new Block('topics/votingRecordComparison.inc');
$votingComparison->topicList = $topicList;
$template->blocks[] = $votingComparison;


$list = new Block('topics/topicList.inc');
$list->topicList = $topicList;
if (isset($tag)) {
	$list->tag = $tag;
}
$template->blocks[] = $list;

echo $template->render();
