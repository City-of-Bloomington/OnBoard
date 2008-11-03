<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param topicList $this->topicList
 */
?>
<topics>
<?php
	foreach($this->topicList as $topic)
	{
		$type = View::escape($topic->getTopicType());
		$number = View::escape($topic->getNumber());
		$date = $topic->getDate('n/j/Y');
		$description = View::escape($topic->getDescription());
		$synopsis = View::escape($topic->getSynopsis());
		echo "
		<topic id=\"{$topic->getId()}\">
			<number>$number</number>
			<date>{$topic->getDate('Y-m-d')}</date>
			<description>$description</description>
			<synopsis>$synopsis</synopsis>
		</topic>
		";
	}
?>
</topics>
