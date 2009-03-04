<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param TopicList $this->topicList
 * @param Committee $this->committee (optional)
 * @param Tag $this->tag
 */
$userCanEdit = userHasRole(array('Administrator','Clerk'));

$addTopicButton = '';
if (isset($this->committee) && $userCanEdit)
{
	$url = new URL(BASE_URL.'/topics/addTopic.php');
	$url->committee_id = $this->committee->getId();
	$addTopicButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}

if (count($this->topicList) || $userCanEdit)
{
	$title = isset($this->tag) ? View::escape($this->tag->getName()) : '';
	echo "
	<div class=\"interfaceBox topicList\">
		<h1>$addTopicButton $title Legislation</h1>
	";
		foreach ($this->topicList as $topic)
		{
			$editButton = '';
			if ($userCanEdit)
			{
				$url = new URL(BASE_URL.'/topics/updateTopic.php');
				$url->topic_id = $topic->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}
			$type = View::escape($topic->getTopicType());
			$number = View::escape($topic->getNumber());
			$date = $topic->getDate('n/j/Y');
			$description = View::escape($topic->getDescription());
			$synopsis = View::escape($topic->getSynopsis());
			echo "
			<div class=\"topic\">
				<h3>$editButton <a href=\"{$topic->getURL()}\">$number</a> <span style=\"font-size:.8em; \" > $date</span></h3>
				<p>$description</p>
			</div>
			";
		}
	echo "
		</table>
	</div>
	";
}
