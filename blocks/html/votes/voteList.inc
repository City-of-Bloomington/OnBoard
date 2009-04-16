<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Topic $this->topic
 * @param VoteList $this->voteList
 */
?>
<div class="interfaceBox votes">
	<h1><?php
			if (isset($this->topic) && userHasRole(array('Administrator','Clerk'))) {
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
			<th>Date</th>
			<?php
				$positions = VotingRecord::getPossiblePositions();
				foreach ($positions as $position) {
					$position = ucfirst($position);
					echo "<th>$position</th>";
				}
			?>
			<th>Final Outcome</th>
		</tr>
		<?php
			foreach ($this->voteList as $vote) {
				$editButton = '';
				if (userHasRole(array('Administrator','Clerk'))) {
					$url = new URL(BASE_URL.'/votes/updateVote.php');
					$url->vote_id = $vote->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}

				echo "
				<tr><th>$editButton</th>
					<th><a href=\"{$vote->getURL()}\">{$vote->getVoteType()}</a></th>
					<td>{$vote->getDate('n/j/Y')}</td>
				";
				foreach ($positions as $position) {
					echo '<td>'.count($vote->getVotingRecords($position)).'</td>';
				}
				echo "
					<td>{$vote->getOutcome()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
