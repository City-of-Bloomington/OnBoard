<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->terms
 */
use Application\Models\Person;
use Blossom\Classes\View;
$title = $this->title ? View::escape($this->title) : $this->_(['labels.term', 'labels.terms', 2]);
?>
<div>
	<h2><?php echo $title; ?></h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_(['labels.committee', 'labels.committees', 1]); ?></th>
				<th><?php echo $this->_('labels.appointed_by'); ?></th>
				<th><?php echo $this->_(['labels.term', 'labels.terms', 1]); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
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
				$committee = $term->getSeat()->getCommittee();
				$personName    = View::escape($term->getPerson()->getFullname());
				$committeeName = View::escape($committee->getName());
				$appointer     = View::escape($term->getSeat()->getAppointer());

				echo "
				<tr><td><a href=\"{$term->getPerson()->getUri()}\">$personName</a></td>
					<td><a href=\"{$committee->getUri()}\">$committeeName</a></td>
					<td>$appointer</td>
					<td>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}</td>
					<td>$editButton $deleteButton</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
