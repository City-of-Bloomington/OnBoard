<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

$voteList = new VoteList();
$voteList->find();

$template = new Template();
$template->blocks[] = new Block('votes/voteList.inc',array('voteList'=>$voteList));
echo $template->render();