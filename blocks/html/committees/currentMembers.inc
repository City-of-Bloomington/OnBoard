<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\Url;
use Blossom\Classes\View;
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<div>
	<h2><?php echo $this->_(['labels.current_member', 'labels.current_members', 2]); ?></h2>
	<table>
		<thead>
			<tr><th><?php echo $this->_('labels.name'); ?></th>
				<th><?php echo $this->_('labels.appointed_by'); ?></th>
				<th colspan="2"><?php echo $this->_(['labels.term','labels.term', 1]); ?></th>
				<th colspan="2"><?php echo $this->_(['labels.office','labels.offices', 2]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$currentUrl = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			$helper = $this->template->getHelper('buttonLink');

			foreach ($this->committee->getSeats(time()) as $seat) {
				$appointer = View::escape($seat->getAppointer());

				//--------------------------------------------------------
				// Show all the current terms
				//--------------------------------------------------------
				$terms = array();
				$c = 0;
				foreach ($seat->getCurrentTerms() as $term) {
					$c++;

					$editTermButton = '';
					$addTermButton  = '';
					if (Person::isAllowed('terms', 'edit')) {
						$editTermButton = $helper->buttonLink(
							BASE_URI."/terms/update?term_id={$term->getId()};return_url=$currentUrl",
							$this->translate('labels.edit'),
							'edit',
							ButtonLink::SIZE_ICON
						);
						$addTermButton = $helper->buttonLink(
							BASE_URI."/terms/update?seat_id={$seat->getId()};person_id={$term->getPerson_id()};return_url=$currentUrl",
							$this->translate('labels.add_term'),
							'add'
						);
					}

					$addOfficeButton  = '';
					$editOfficeButton = '';
					if (Person::isAllowed('offices', 'edit')) {
						$addOfficeButton = $helper->buttonLink(
							BASE_URI."/offices/update?committee_id={$seat->getCommittee()->getId()};person_id={$term->getPerson_id()}",
							$this->translate('labels.add_office'),
							'add'
						);
					}

					$offices = array();
					foreach ($term->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d')) as $office) {
						$editOfficeButton = '';
						if (Person::isAllowed('offices','edit')) {
							$editOfficeButton = $helper->buttonLink(
								BASE_URI.'/offices/update?office_id='.$office->getId(),
								$this->translate('labels.edit_office'),
								'edit'
							);
						}
						$offices[] = View::escape($office->getTitle());
					}
					$offices = implode(',',$offices);

					$fullname = View::escape($term->getPerson()->getFullname());
					echo "
					<tr><td><a href=\"{$term->getPerson()->getURL()}\">$fullname</a></td>
						<td>$appointer</td>
						<td>{$term->getTerm_start(DATE_FORMAT)} - {$term->getTerm_end(DATE_FORMAT)}
							$editTermButton
						</td>
						<td>$addTermButton</td>
						<td>$offices</td>
						<td>$addOfficeButton $editOfficeButton</td>
					</tr>
					";
				}
				//--------------------------------------------------------
				// Show all the vacancies
				//--------------------------------------------------------
				$t = $seat->getMaxCurrentTerms();
				$addTermButton = '';
				if (Person::isAllowed('terms', 'edit')) {
					$addTermButton = $helper->buttonLink(
						BASE_URI."/terms/update?seat_id={$seat->getId()};return_url=$currentUrl",
						$this->translate('labels.add_term'),
						'add'
					);
				}
				for ($c; $c < $t; $c++) {
					echo "
					<tr><td>( {$this->_('labels.vacancy')} )</td>
						<td>$appointer</td>
						<td></td>
						<td>$addTermButton</td>
						<td></td>
						<td></td>
					</tr>
					";
				}
			}
		?>
		</tbody>
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
