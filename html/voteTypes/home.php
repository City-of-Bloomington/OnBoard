<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$voteTypeList = new VoteTypeList();
$voteTypeList->find();

$template = new Template();
$template->title = 'Vote Types';
$template->blocks[] = new Block('voteTypes/voteTypeList.inc',array('voteTypeList'=>$voteTypeList));
echo $template->render();
