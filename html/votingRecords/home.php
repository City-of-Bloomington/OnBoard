<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$votingRecordList = new VotingRecordList();
$votingRecordList->find();

$template = new Template();
$template->title = 'Voting Records';
$template->blocks[] = new Block('votingRecords/votingRecordList.inc',
								array('votingRecordList'=>$votingRecordList));
echo $template->render();
