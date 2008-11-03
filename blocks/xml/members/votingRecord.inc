<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
?>
<votingRecord>
<?php
	foreach($this->user->getVotingRecords() as $vote)
	{
		$number = View::escape($vote->getTopic()->getNumber());
		echo "
		<vote>
			<date>{$vote->getDate('n/j/Y')}</date>
			<memberVote>{$vote->getMemberVote()}</memberVote>
			<topic id=\"{$vote->getTopic()->getId()}\">$number</topic>
		</vote>
		";
	}
?>
</votingRecord>
