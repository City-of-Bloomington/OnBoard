<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TopicTypeList $this->topicTypeList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole(array('Administrator'))) {
				echo "<button type=\"button\" class=\"add\" onclick=\"document.location.href='".BASE_URL."/topicTypes/addTopicType.php';\">
				Add
				</button>
				";
			}
		?>
		TopicTypes
	</h1>
	<ul><?php
			foreach ($this->topicTypeList as $topicType) {
				$editButton = '';
				if (userHasRole(array('Administrator'))) {
					$url = new URL(BASE_URL.'/topicTypes/updateTopicType.php');
					$url->topicType_id = $topicType->getId();
					$editButton = "<button type=\"button\" class=\"edit\" onclick=\"$url\">
					Edit
					</button>
					";
				}

				$name = View::escape($topicType->getName());
				echo "<li>$editButton $name</li>";
			}
		?>
	</ul>
</div>
