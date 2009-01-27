<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$topicTypeList = new TopicTypeList();
$topicTypeList->find();

$template = new Template();
$template->title = 'Topic Types';
$template->blocks[] = new Block('topicTypes/topicTypeList.inc',
								array('topicTypeList'=>$topicTypeList));
echo $template->render();
