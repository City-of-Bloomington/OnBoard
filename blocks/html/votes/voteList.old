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
				echo "<a class=\"add button\" href=\"".BASE_URL."/votes/addVote.php\">Add</a>";
			}
		?>
		Votes
	</h1>
	<ul><?php
			foreach($this->voteList as $vote)
			{
				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/votes/updateVote.php?id={$vote->getId()}\">Edit</a>" : '';
				echo "<li>$editButton $vote</li>";
			}
		?>
	</ul>
</div>