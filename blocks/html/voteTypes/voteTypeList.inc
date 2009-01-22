<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole('Administrator'))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/voteTypes/addVoteType.php\">Add</a>";
			}
		?>
		VoteTypes
	</h1>
	<ul><?php
			foreach ($this->voteTypeList as $voteType)
			{
				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/voteTypes/updateVoteType.php?id={$voteType->getId()}\">Edit</a>" : '';
				echo "<li>$editButton $voteType</li>";
			}
		?>
	</ul>
</div>