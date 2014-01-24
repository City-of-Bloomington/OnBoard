<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Topic $this->topic
 */
use Application\Models\Person;
use Blossom\Classes\View;

$type        = View::escape($this->topic->getTopicType());
$number      = View::escape($this->topic->getNumber());
$description = View::escape($this->topic->getDescription());
$synopsis    = View::escape($this->topic->getSynopsis());
$date        = $this->topic->getDate(DATE_FORMAT);

$editButton = '';
if (Person::isAllowed('topics', 'edit')) {
	$editButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-edit"></i> %s</a>',
		BASE_URI.'/topics/update?topic_id='.$this->topic->getId(),
		$this->translate('labels.edit_topic')
	);
}

$tags = array();
foreach ($this->topic->getTags() as $tag) {
	$tags[] = View::escape($tag->getName());
}
$tags = implode(', ',$tags);
if ($tags) {
	$tags = "<p><strong>Tags: </strong>$tags</p>";
}

echo "
<div>
	<h2><a href=\"{$this->topic->getUri()}\">$type $number ($date)</a>
		$editButton
	</h2>
	<h3>$description</h3>
	<p>$synopsis</p>
	$tags
</div>
";
