<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->seats
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
use Blossom\Classes\View;

$userCanEdit = Person::isAllowed('seats', 'edit');
?>
<div>
	<h2><?php
			echo $this->_(['labels.seat', 'labels.seats', 2]);

			if (isset($this->committee) && $userCanEdit) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/seats/update?committee_id='.$this->committee->getId(),
					$this->translate('labels.add_seat')
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
			</tr>
		</thead>
		<tbody>
		<?php
			$currentSeatId = !empty($_GET['seat_id']) ? $_GET['seat_id'] : null;
			$editButton = '';

			foreach ($this->seats as $seat) {
				if ($userCanEdit) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/seats/update?seat_id='.$seat->getId(),
						$this->translate('labels.edit_seat')
					);
				}
				$name = View::escape($seat->getName());
				$appointer = View::escape($seat->getAppointer());
				$class = $seat->getId() == $currentSeatId ? 'class="current"' : '';
				echo "
				<tr $class>
					<td>$editButton</td>
					<td><a href=\"{$seat->getUrl()}\">$name</a></td>
					<td>$appointer</td>
					<td>{$seat->getMaxCurrentTerms()}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
