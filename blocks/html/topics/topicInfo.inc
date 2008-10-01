<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->topic
 */
$type = View::escape($this->topic->getTopicType());
$number = View::escape($this->topic->getNumber());
$date = $this->topic->getDate('n/j/Y');
$description = View::escape($this->topic->getDescription());
$synopsis = View::escape($this->topic->getSynopsis());

$editButton = '';
if (userHasRole(array('Administrator','Clerk')))
{
	$url = new URL(BASE_URL.'/topics/updateTopic.php');
	$url->topic_id = $this->topic->getId();
	$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
}
	$url = new URL(BASE_URL.'/topics/viewTopic.php');
	$url->topic_id = $this->topic->getId();
echo "
<div class=\"interfaceBox\">
	<h1>$editButton
		<a href=\"{$this->topic->getURL()}\">$type $number ($date)</a>
	</h1>
	<h2>$description</h2>
	<p>$synopsis</p>
</div>
";
