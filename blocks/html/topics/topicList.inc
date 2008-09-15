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
				echo "<a class=\"add button\" href=\"".BASE_URL."/topics/addTopic.php\">Add</a>";
			}
		?>
		Topics
	</h1>
	<ul><?php
			foreach($this->topicList as $topic)
			{
				$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/topics/updateTopic.php?id={$topic->getId()}\">Edit</a>" : '';
				echo "<li>$editButton $topic</li>";
			}
		?>
	</ul>
</div>