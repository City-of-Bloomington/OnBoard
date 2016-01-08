<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->allocations
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$userCanEdit   = Person::isAllowed('allocations', 'edit');
$userCanDelete = Person::isAllowed('allocations', 'delete');

$helper = $this->template->getHelper('buttonLink');
?>
<div>
	<h2><?= $this->_(['allocation', 'allocations', 2]); ?></h2>
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

			foreach ($this->allocations as $allocation) {
                $id = $allocation->getId();

				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI."/allocations/update?allocation_id=$id",
						$this->_('allocation_edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}
				$name      = self::escape($allocation->getName());
				$appointer = self::escape($allocation->getAppointer());

				$uri = BASE_URI."/allocations/view?allocation_id=$id";
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$uri\">$name</a></td>
					<td>$appointer</td>
					<td>{$allocation->getStartDate(DATE_FORMAT)}</td>
					<td>{$allocation->getEndDate(DATE_FORMAT)}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
        if (isset($this->committee) && $userCanEdit) {
            echo $helper->buttonLink(
                BASE_URI."/allocations/update?committee_id={$this->committee->getId()}",
                $this->translate('allocation_add'),
                'add'
            );
        }
	?>
</div>
