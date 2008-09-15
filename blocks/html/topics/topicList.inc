<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$addTopicButton = '';
if (isset($this->committee) && userHasRole('Administrator'))
{
	$url = new URL(BASE_URL.'/topics/addTopic.php');
	$url->committee_id = $this->committee->getId();
	$addTopicButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}

?>
<div class="interfaceBox">
	<h1><?php echo $addTopicButton; ?>
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