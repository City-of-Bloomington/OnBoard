<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->terms
 */
use Application\Models\Person;
use Blossom\Classes\View;

$tableRows = ['current'=>'','past'=>''];

foreach ($this->terms as $term) {
	$editButton = '';
	$deleteButton = '';
	if (Person::isAllowed('terms', 'edit')) {
		$editButton = sprintf(
			'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
			BASE_URI."/terms/update?term_id={$term->getId()}",
			$this->translate('labels.edit')
		);

		if ($term->isSafeToDelete()) {
			$deleteButton = sprintf(
				'<a class="fa fa-times" href="%s"><i class="hidden-label">%s</i></a>',
				BASE_URI."/terms/delete?term_id={$term->getId()}",
				$this->translate('labels.delete')
			);
		}
	}
	$personName = View::escape($term->getPerson()->getFullname());
	$committee = $term->getSeat()->getCommittee();
	$committeeName = View::escape($committee->getName());
	$appointer = View::escape($term->getSeat()->getAppointer()->getName());

	// We want to display the terms as two tables...One for current, and one for past
	// Split the rows up into current and past terms.
	$now = time();
	$table = ($term->getTerm_start('U') <= $now && $now <= $term->getTerm_end('U'))
			? 'current'
			: 'past';
	$tableRows[$table].= "
	<tr><td><a href=\"{$term->getPerson()->getUri()}\">$personName</a></td>
		<td><a href=\"{$committee->getUri()}\">$committeeName</a></td>
		<td>$appointer</td>
		<td>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}</td>
		<td>$editButton $deleteButton</td>
	</tr>
	";
}
?>
<div>
	<?php
		$thead = "
		<thead>
			<tr><th></th>
				<th>{$this->_(['labels.committee', 'labels.committees', 1])}</th>
				<th>{$this->_('labels.appointed_by')}</th>
				<th>{$this->_(['labels.term', 'labels.terms', 1])}</th>
				<th></th>
			</tr>
		</thead>
		";
		if ($tableRows['current']) {
			echo "
			<h2>Current Terms</h2>
			<table>
			$thead
			<tbody>
				$tableRows[current]
			</tbody></table>
			";
		}

		if ($tableRows['past']) {
			echo "
			<h2>Past Terms</h2>
			<table>
			$thead
			<tbody>
				$tableRows[past]
			</tbody>
			</table>
			";
		}
	?>
</div>
