<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Michelf\MarkdownExtra;

$name = self::escape($this->seat->getName());

$helper = $this->template->getHelper('buttonLink');
$editSeatButton = '';
if (Person::isAllowed('seats', 'edit')) {
	$editSeatButton = $helper->buttonLink(
		BASE_URI.'/seats/update?seat_id='.$this->seat->getId(),
		$this->translate('seat_edit'),
		'edit'
	);
}
echo "
<div>
	<h2>$name $editSeatButton</h2>
	<table>
		<tr><th>{$this->_('startDate')}</th>
			<td>{$this->seat->getStartDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_('endDate')}</th>
			<td>{$this->seat->getEndDate(DATE_FORMAT)}</td>
		</tr>
    </table>
</div>
";
