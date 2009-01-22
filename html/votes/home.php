<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$voteList = new VoteList();
$voteList->find();

$template = new Template();
$template->blocks[] = new Block('votes/voteList.inc',array('voteList'=>$voteList));
echo $template->render();