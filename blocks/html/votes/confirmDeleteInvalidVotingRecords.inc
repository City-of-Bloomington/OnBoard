<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
?>
<h1>Update Vote</h1>
<div id="warning">
	<h1>You are about to delete voting records.</h1>
	<p>These voting records are no longer valid.  The date of the vote no longer occurs
		during the terms for the voting records.</p>
	<h2>Vote Date: <?php echo $this->vote->getDate('n/j/Y'); ?></h2>
	<table>
		<tr><th>Member</th>
			<th>Term</th>
			<th>Position</th>
		</tr>
		<?php
			foreach ($this->vote->getInvalidVotingRecords() as $votingRecord) {
				$fullname = View::escape($votingRecord->getPerson()->getFullname());
				$termStart = $votingRecord->getTerm()->getTerm_start('n/j/Y');
				$termEnd = $votingRecord->getTerm()->getTerm_end('n/j/Y');
				echo "
				<tr><td><a href=\"{$votingRecord->getPerson()->getURL()}\">$fullname</a></td>
					<td>$termStart - $termEnd</td>
					<td>{$votingRecord->getPosition()}</td>
				</tr>
				";
			}
		?>
	</table>

	<button type="button" onclick="document.location.href='<?php echo $_SERVER['SCRIPT_NAME']; ?>?confirm=true';">
		Update vote and delete invalid voting records
	</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->vote->getTopic()->getURL(); ?>';">
		Cancel
	</button>
</div>
