<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
?>
<div class="interfaceBox">
	<h1>Voting Record for <?php echo View::escape($this->user->getFullname()); ?></h1>
	<table>
	<tr><th>Date</th>
		<th>Vote</th>
		<th>Number</th>
		<th>Description</th>
	</tr>
	<?php
		foreach ($this->user->getVotingRecords() as $vote)
		{
			$number = View::escape($vote->getTopic()->getNumber());
			$description = View::escape($vote->getTopic()->getDescription());
			echo "
			<tr><td>{$vote->getDate('n/j/Y')}</td>
				<td>{$vote->getMemberVote()}</td>
				<td><a href=\"{$vote->getTopic()->getURL()}\">$number</a></td>
				<td>$description</td>
			</tr>
			";
		}
	?>
	</table>
</div>
