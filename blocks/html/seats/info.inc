<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$name         = View::escape($this->seat->getName());
$appointer    = View::escape($this->seat->getAppointer());
$committee    = View::escape($this->seat->getCommittee()->getName());
$requirements = View::escape($this->seat->getRequirements());

if ($requirements) {
	$markdown = new MarkdownExtra();
	$requirements = $markdown->transform($requirements);
}

$helper = $this->template->getHelper('buttonLink');
$addTermButton = '';
$editSeatButton = '';
if (Person::isAllowed('seats', 'edit')) {
	$addTermButton = $helper->buttonLink(
		BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
		$this->translate('labels.add_term'),
		'add'
	);
	$editSeatButton = $helper->buttonLink(
		BASE_URI.'/seats/update?seat_id='.$this->seat->getId(),
		$this->translate('labels.edit_seat'),
		'edit'
	);
}
echo "
<div>
	<h2>$name $editSeatButton $addTermButton</h2>
	<table>
		<tr><th>{$this->_('labels.appointed_by')}</th>
			<td>$appointer</td>
		</tr>
		<tr><th>{$this->_(['labels.committee', 'labels.committees', 1])}</th>
			<td>$committee</td>
		</tr>
		<tr><th>{$this->_('labels.maxCurrentTerms')}</th>
			<td>{$this->seat->getMaxCurrentTerms()}</td>
		</tr>
		<tr><th>{$this->_('labels.date_start')}</th>
			<td>{$this->seat->getStartDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_('labels.date_end')}</th>
			<td>{$this->seat->getEndDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_(['labels.requirement', 'labels.requirements', 2])}</th>
			<td>$requirements</td>
		</tr>
	</table>
</div>
";
