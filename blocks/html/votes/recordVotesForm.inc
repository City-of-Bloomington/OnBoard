<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
?>
<h1>Add/Edit Voting Records</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Voting Record Info</legend>
	<input name="vote_id" type="hidden" value="<?php echo $this->vote->getId(); ?>" />

	<table>
	<tr><th>Seat</th>
		<th>Member</th>
		<th>Yes</th>
		<th>No</th>
		<th>Abstain</th>
		<th>Absent</th>
	</tr>
	<?php
		foreach($this->vote->getCommittee()->getCurrentMembers() as $member)
		{
			$title = View::escape($member->getSeat()->getTitle());
			$memberName = View::escape("{$member->getFirstname()} {$member->getLastname()}");
			$votingRecord = $this->vote->getVotingRecord($member);

			$yesChecked = $votingRecord->getMemberVote()=='yes' ? ' checked="checked"' : '';
			$noChecked = $votingRecord->getMemberVote()=='no' ? ' checked="checked"' : '';
			$abstainChecked = $votingRecord->getMemberVote()=='abstain' ? ' checked="checked"' : '';
			$absentChecked = $votingRecord->getMemberVote()=='absent' ? ' checked="checked"' : '';

			echo "
			<tr><th>$title</th>
				<th>$memberName</th>
				<td><input name=\"votingRecord[{$member->getId()}]\"
							type=\"radio\"
							value=\"yes\"
							$yesChecked />
				</td>
				<td><input name=\"votingRecord[{$member->getId()}]\"
							type=\"radio\"
							value=\"no\"
							$noChecked />
				</td>
				<td><input name=\"votingRecord[{$member->getId()}]\"
							type=\"radio\"
							value=\"abstain\"
							$abstainChecked />
				</td>
				<td><input name=\"votingRecord[{$member->getId()}]\"
							type=\"radio\"
							value=\"absent\"
							$absentChecked />
				</td>
			</tr>
			";
		}
	?>

	</table>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->vote->getTopic()->getURL(); ?>';">Cancel</button>
</fieldset>
</form>
