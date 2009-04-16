<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET tag_id
 */
try {
	$tag = new Tag($_GET['tag_id']);

	$template = new Template();
	$template->title = 'Tag: '.$tag->getName();

	$template->blocks[] = new Block('tags/breadcrumbs.inc',array('tag'=>$tag));
	$template->blocks[] = new Block('tags/tagInfo.inc',array('tag'=>$tag));
	$template->blocks[] = new Block('topics/topicList.inc',array('topicList'=>$tag->getTopics()));
	echo $template->render();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/tags');
	exit();
}
