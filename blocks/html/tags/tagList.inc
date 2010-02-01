<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TagList $this->tagList
 */
$userCanEdit = userHasRole(array('Administrator','Clerk'));
?>
<div class="interfaceBox">
	<h1><?php
			if ($userCanEdit) {
				echo "<button type=\"button\" class=\"add\" onclick=\"document.location.href'".BASE_URL."/tags/addTag.php';\">
				Add Tag
				</button>";
			}
		?>
		Tags
	</h1>
	<ul><?php
			foreach ($this->tagList as $tag) {
				$editButton = '';
				$deleteButton = '';
				if ($userCanEdit) {
					$url = new URL(BASE_URL.'/tags/updateTag.php');
					$url->tag_id = $tag->getId();
					$editButton = "<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$url';\">
					Edit Tag
					</button>
					";

					$url = new URL(BASE_URL.'/tags/deleteTag.php');
					$url->tag_id = $tag->getId();
					$deleteButton = "
					<button type=\"button\" class=\"delete\" onclick=\"FRAMEWORK.deleteConfirmation('$url');\">
						Delete Tag
					</button>";
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
