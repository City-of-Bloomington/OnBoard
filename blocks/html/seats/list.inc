<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->seats
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;

$userCanEdit = Person::isAllowed('seats', 'edit');
$helper = $this->template->getHelper('buttonLink');
?>
<div>
	<h2><?php
			echo $this->_(['labels.seat', 'labels.seats', 2]);

			if (isset($this->committee) && $userCanEdit) {
				echo $helper->buttonLink(
					BASE_URI.'/seats/update?committee_id='.$this->committee->getId(),
					$this->translate('labels.add_seat'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_('labels.name'); ?></th>
				<th><?php echo $this->_(['labels.appointer', 'labels.appointers', 1]); ?></th>
				<th><?php echo $this->_('labels.maxCurrentTerms'); ?></th>
				<th><?php echo $this->_('labels.date_start'); ?></th>
				<th><?php echo $this->_('labels.date_end'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$currentSeatId = !empty($_GET['seat_id']) ? $_GET['seat_id'] : null;
			$editButton = '';

			foreach ($this->seats as $seat) {
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/seats/update?seat_id='.$seat->getId(),
						$this->translate('labels.edit_seat'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}
				$name      = View::escape($seat->getName());
				$appointer = View::escape($seat->getAppointer());
				$class = $seat->getId() == $currentSeatId ? 'class="current"' : '';
				echo "
				<tr $class>
					<td>$editButton</td>
					<td><a href=\"{$seat->getUrl()}\">$name</a></td>
					<td>$appointer</td>
					<td>{$seat->getMaxCurrentTerms()}</td>
					<td>{$seat->getStartDate(DATE_FORMAT)}</td>
					<td>{$seat->getEndDate(DATE_FORMAT)}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
