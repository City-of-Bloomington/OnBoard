<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;
use Application\Models\Person;
?>
<div>
	<h2><?php echo $this->_('labels.current_members'); ?></h2>
	<table>
		<thead>
			<tr><th><?php echo $this->_('labels.name'); ?></th>
				<th><?php echo $this->_('labels.appointed_by'); ?></th>
				<th><?php echo $this->_(['labels.term','labels.term', 1]); ?></th>
				<th colspan="2"><?php echo $this->_(['labels.office','labels.offices', 2]); ?></th>
			</tr>
		</thead>
		<?php
			foreach ($this->committee->getSeats(time()) as $seat) {
				$appointer = View::escape($seat->getAppointer());

				//--------------------------------------------------------
				// Show all the current terms
				//--------------------------------------------------------
				$terms = array();
				$c = 0;
				foreach ($seat->getCurrentTerms() as $term) {
					$c++;

					$addOfficeButton  = '';
					$editOfficeButton = '';
					if (Person::isAllowed('offices', 'edit')) {
						$addOfficeButton = sprintf(
							'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
							BASE_URI."/offices/update?committee_id={$seat->getCommittee()->getId()};person_id={$term->getPerson_id()}",
							$this->translate('labels.add_office')
						);
					}

					$offices = array();
					foreach ($term->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d')) as $office) {
						$editOfficeButton = '';
						if (Person::isAllowed('offices','edit')) {
							$editOfficeButton = sprintf(
								'<a class="btn" href="%s"><i class="fa fa-edit"></i> %s</a>',
								BASE_URI.'/offices/update?office_id='.$office->getId(),
								$this->translate('labels.edit_office')
							);
						}
						$offices[] = View::escape($office->getTitle());
					}
					$offices = implode(',',$offices);

					$fullname = View::escape($term->getPerson()->getFullname());
					echo "
					<tbody>
					<tr><td><a href=\"{$term->getPerson()->getURL()}\">$fullname</a></td>
						<td>$appointer</td>
						<td>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}</td>
						<td>$offices</td>
						<td>$addOfficeButton $editOfficeButton</td>
					</tr>
					</tbody>
					";
				}
				//--------------------------------------------------------
				// Show all the vacancies
				//--------------------------------------------------------
				$t = $seat->getMaxCurrentTerms();
				for ($c; $c < $t; $c++) {
					echo "
					<tbody>
					<tr><td>( {$this->_('labels.vacancy')} )</td>
						<td>$appointer</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
					";
				}
			}
		?>
	</table>
	<div>
		<?php
			echo "
			<a href=\"{$this->committee->getUri()};members=past\">
				{$this->_('labels.view_past_members')}
			</a>
			";
		?>
	</div>
</div>
