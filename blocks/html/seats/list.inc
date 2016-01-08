<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->seats
 * @param Committee $this->committee (optional)
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
				<th><?= $this->_(['appointer', 'appointers', 1]); ?></th>
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
				$name      = self::escape($seat->getName());
				$appointer = self::escape($seat->getAppointer());
				$uri  = BASE_URI."/seats/view?seat_id=$seat_id";

				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$uri\">$name</a></td>
					<td>$appointer</td>
					<td>{$seat->getStartDate(DATE_FORMAT)}</td>
					<td>{$seat->getEndDate(DATE_FORMAT)}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
        if (isset($this->committee) && Person::isAllowed('seats', 'add')) {
            echo $helper->buttonLink(
                BASE_URI."/seats/update?committee_id={$this->committee->getId()}",
                $this->translate('seat_add'),
                'add'
            );
        }
	?>
</div>
