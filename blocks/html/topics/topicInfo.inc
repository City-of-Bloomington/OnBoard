<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
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
if (userHasRole(array('Administrator','Clerk'))) {
	$url = new URL(BASE_URL.'/topics/updateTopic.php');
	$url->topic_id = $this->topic->getId();
	$editButton = "<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$url';\">
	Edit
	</button>";
}
	$url = new URL(BASE_URL.'/topics/viewTopic.php');
	$url->topic_id = $this->topic->getId();

$tags = array();
foreach ($this->topic->getTags() as $tag) {
	$tags[] = View::escape($tag->getName());
}
$tags = implode(', ',$tags);
if ($tags) {
	$tags = "<p><strong>Tags: </strong>$tags</p>";
}

echo "
<div class=\"interfaceBox legislation\">
	<h1>$editButton
		<a href=\"{$this->topic->getURL()}\">$type $number ($date)</a>
	</h1>
	<h2>$description</h2>
	<p>$synopsis</p>
	$tags
</div>
";
