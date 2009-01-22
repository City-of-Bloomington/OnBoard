<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$topicList = new TopicList();
$topicList->find();

$template = new Template();
$template->blocks[] = new Block('topics/topicList.inc',array('topicList'=>$topicList));
echo $template->render();