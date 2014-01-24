<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->topics
 */
use Application\Models\Person;
use Blossom\Classes\View;

$userCanEdit = Person::isAllowed('topics', 'edit');

foreach ($this->topics as $topic) {
	$editButton = '';
	if ($userCanEdit) {
		$editButton = sprintf(
			'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
			BASE_URI.'/topics/update?topic_id='.$topic->getId(),
			$this->translate('labels.edit')
		);
	}
	$date        = $topic->getDate('n/j/Y');
	$type        = View::escape($topic->getTopicType());
	$number      = View::escape($topic->getNumber());
	$description = View::escape($topic->getDescription());
	$synopsis    = View::escape($topic->getSynopsis());

	echo "
	<div>
		<h3>$editButton
			<a href=\"{$topic->getUri()}\">$number</a>
			$date
		</h3>
		<p>$description</p>
	</div>
	";
}
