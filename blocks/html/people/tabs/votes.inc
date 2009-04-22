<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @oaram GET voteType
 */
$currentVoteType = (isset($_GET['voteType']) && $_GET['voteType']=='all') ? 'all' : 'final';

$allURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$allURL->voteType = 'all';
$finalURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$finalURL->voteType = 'final';

$finalClass = $currentVoteType=='final' ? 'class="current"' : '';
$allClass = $currentVoteType=='all' ? 'class="current"' : '';

$voteTypes = new VoteTypeList();
$voteTypes->find();
echo "
<ul class=\"tabBar\">
	<li><a href=\"$finalURL\" $finalClass>Final Votes</a></li>
	<li><a href=\"$allURL\" $allClass>All Votes</a></li>
</ul>
";

if ($currentVoteType=='final') {
	$this->voteType = VoteType::getFinalVotetype();
}
include APPLICATION_HOME.'/blocks/html/people/votingRecord.inc';
