<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$topicTypeList = new TopicTypeList();
$topicTypeList->find();

$template = new Template();
$template->blocks[] = new Block('topicTypes/topicTypeList.inc',array('topicTypeList'=>$topicTypeList));
echo $template->render();