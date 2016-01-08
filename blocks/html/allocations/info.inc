<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Allocation $this->allocation
 */
use Application\Models\Person;
use Michelf\MarkdownExtra;

$id = $this->allocation->getId();

$name         = self::escape($this->allocation->getName());
$appointer    = self::escape($this->allocation->getAppointer());
$committee    = self::escape($this->allocation->getCommittee()->getName());
$requirements = self::escape($this->allocation->getRequirements());

if ($requirements) {
	$markdown = new MarkdownExtra();
	$requirements = $markdown->transform($requirements);
}

$helper = $this->template->getHelper('buttonLink');
$editAllocationButton = '';
if (Person::isAllowed('allocations', 'edit')) {
	$editAllocationButton = $helper->buttonLink(
		BASE_URI."/allocations/update?allocation_id=$id",
		$this->translate('allocation_edit'),
		'edit'
	);
}
echo "
<div>
	<h2>$name $editAllocationButton</h2>
	<table>
		<tr><th>{$this->_('appointed_by')}</th>
			<td>$appointer</td>
		</tr>
		<tr><th>{$this->_(['committee', 'committees', 1])}</th>
			<td>$committee</td>
		</tr>
		<tr><th>{$this->_('startDate')}</th>
			<td>{$this->allocation->getStartDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_('endDate')}</th>
			<td>{$this->allocation->getEndDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_(['requirement', 'requirements', 2])}</th>
			<td>$requirements</td>
		</tr>
	</table>
</div>
";
