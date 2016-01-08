<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->seats
 * @param Committee $this->allocation (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$helper = $this->template->getHelper('buttonLink');
?>
<div>
	<h2><?= $this->_(['seat', 'seats', 2]); ?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('name'); ?></th>
				<th><?= $this->_('startDate'); ?></th>
				<th><?= $this->_('endDate'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			$editButton = '';

			foreach ($this->seats as $seat) {
                $seat_id = $seat->getId();

				if (Person::isAllowed('seats', 'edit')) {
					$editButton = $helper->buttonLink(
						BASE_URI."/seats/update?seat_id=$seat_id",
						$this->translate('seat_edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}
				$name = self::escape($seat->getName());
				$uri  = BASE_URI."/seats/view?seat_id=$seat_id";

				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$uri\">$name</a></td>
					<td>{$seat->getStartDate(DATE_FORMAT)}</td>
					<td>{$seat->getEndDate(DATE_FORMAT)}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
        if (isset($this->allocation) && Person::isAllowed('seats', 'add')) {
            echo $helper->buttonLink(
                BASE_URI."/seats/update?allocation_id={$this->allocation->getId()}",
                $this->translate('seat_add'),
                'add'
            );
        }
	?>
</div>
