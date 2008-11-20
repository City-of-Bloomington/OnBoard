<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TagList $this->tagList
 */
$userCanEdit = userHasRole(array('Administrator','Clerk'));
?>
<div class="interfaceBox">
	<h1>
		<?php
			if ($userCanEdit)
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/tags/addTag.php\">Add Tag</a>";
			}
		?>
		Tags
	</h1>
	<ul><?php
			foreach($this->tagList as $tag)
			{
				$editButton = '';
				$deleteButton = '';
				if ($userCanEdit)
				{
					$url = new URL(BASE_URL.'/tags/updateTag.php');
					$url->tag_id = $tag->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit Tag</a>";

					$url = new URL(BASE_URL.'/tags/deleteTag.php');
					$url->tag_id = $tag->getId();
					$deleteButton = "<button type=\"button\" class=\"delete\" onclick=\"FRAMEWORK.deleteConfirmation('$url');\">Delete Tag</button>";
				}

				$name = View::escape($tag->getName());
				echo "
				<li>$editButton
					$deleteButton
					<a href=\"{$tag->getURL()}\">$name</a>
				</li>
				";
			}
		?>
	</ul>
</div>
