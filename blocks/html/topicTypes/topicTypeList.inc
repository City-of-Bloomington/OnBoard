<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole('Administrator'))
			{
				$url = BASE_URL.'/topicTypes/addTopicType.php';
				echo "<a class=\"add button\" href=\"$url\">Add</a>";
			}
		?>
		Topic Types
	</h1>
	<ul><?php
			foreach($this->topicTypeList as $topicType)
			{
				$editButton = '';
				if (userHasRole('Administrator'))
				{
					$url = new URL(BASE_URL.'/topicTypes/updateTopicType.php');
					$url->topicType_id = $topicType->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}
				echo "
				<li>$editButton
					$topicType
				</li>
				";
			}
		?>
	</ul>
</div>
