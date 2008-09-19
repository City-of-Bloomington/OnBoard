<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole(array('Administrator','Clerk')))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/votingRecords/addVotingRecord.php?vote_id={$this->vote->getId()}\">Add</a>";
			}
		?>
		Voting Records
	</h1>
	<table>
	<?php
			foreach($this->votingRecordList as $votingRecord)
			{
				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/votingRecords/updateVotingRecord.php?id={$votingRecord->getId()}\">Edit</a>" : '';
				echo "
				     <tr><td>$editButton</td>
					<td>{$votingRecord->getMember()}</td>
					<td>{$votingRecord->getMemberVote()}</td>
				    </tr>
				";
			}
		?>
	</table>
</div>