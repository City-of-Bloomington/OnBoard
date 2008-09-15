<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole('Administrator'))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/votingRecords/addVotingRecord.php\">Add</a>";
			}
		?>
		VotingRecords
	</h1>
	<ul><?php
			foreach($this->votingRecordList as $votingRecord)
			{
				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/votingRecords/updateVotingRecord.php?id={$votingRecord->getId()}\">Edit</a>" : '';
				echo "<li>$editButton $votingRecord</li>";
			}
		?>
	</ul>
</div>