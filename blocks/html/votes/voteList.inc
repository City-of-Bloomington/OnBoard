<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Topic $this->topic
 * @param VoteList $this->voteList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (isset($this->topic) && userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/votes/addVote.php');
				$url->topic_id = $this->topic->getId();
				echo "<a class=\"add button\" href=\"$url\">Add</a>";
			}
		?>
		Votes
	</h1>
	<table>
	<tr><th></th>
		<th>Vote</th>
		<th>Vote Date</th>
		<th>Yes</th>
		<th>No</th>
		<th>Absents</th>
		<th>Abstain</th>
		<th>Final Outcome</th>
	</tr>
	<?php
	    foreach($this->voteList as $vote)
		{
			$editButton = '';
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/votes/updateVote.php');
				$url->vote_id = $vote->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}

			echo "
			<tr><td>$editButton</td>
				<td><a href=\"{$vote->getURL()}\">{$vote->getVoteType()}</a></td>
				<td>{$vote->getDate('n/j/Y')}</td>
				<td>".count($vote->getVotingRecords('yes'))."</td>
				<td>".count($vote->getVotingRecords('no'))."</td>
				<td>".count($vote->getVotingRecords('absent'))."</td>
				<td>".count($vote->getVotingRecords('abstain'))."</td>
				<td>{$vote->getOutcome()}</td>
			</tr>
			";
		}
	?>
	</table>
</div>
