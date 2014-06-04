<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->topics
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;

$userCanEdit = Person::isAllowed('topics', 'edit');

foreach ($this->topics as $topic) {
	$editButton = '';
	if ($userCanEdit) {
		$helper = $this->template->getHelper('buttonLink');
		$editButton = $helper->buttonLink(
			BASE_URI.'/topics/update?topic_id='.$topic->getId(),
			$this->translate('labels.edit'),
			'edit',
			ButtonLink::SIZE_ICON
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
