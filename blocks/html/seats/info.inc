<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Michelf\MarkdownExtra;

$name         = self::escape($this->seat->getName());
$appointer    = self::escape($this->seat->getAppointer());
$committee    = self::escape($this->seat->getCommittee()->getName());
$requirements = self::escape($this->seat->getRequirements());
$termLength   = self::escape($this->seat->getTermLength());

if ($requirements) {
	$markdown = new MarkdownExtra();
	$requirements = $markdown->transform($requirements);
}

$helper = $this->template->getHelper('buttonLink');
$editButton   = '';
$deleteButton = '';
if (Person::isAllowed('seats', 'edit')) {
	$editButton = $helper->buttonLink(
		BASE_URI.'/seats/update?seat_id='.$this->seat->getId(),
		$this->_('seat_edit'),
		'edit'
	);
}
if (Person::isAllowed('seats', 'delete') && $this->seat->isSafeToDelete()) {
    $deleteButton = $helper->buttonLink(
        BASE_URI.'/seats/delete?seat_id='.$this->seat->getId(),
        $this->_('seat_delete'),
        'delete'
    );
}

$uri = BASE_URI.'/seats/view?seat_id='.$this->seat->getId();
echo "
<section>
	<h1>$editButton<a href=\"$uri\">$name</a>$deleteButton</h1>
	<table>
        <tr><th>{$this->_('type')}</th>
            <td>{$this->seat->getType()}</td>
        </tr>
		<tr><th>{$this->_('appointed_by')}</th>
			<td>$appointer</td>
		</tr>
		<tr><th>{$this->_(['committee', 'committees', 1])}</th>
			<td>$committee</td>
		</tr>
		<tr><th>{$this->_('startDate')}</th>
			<td>{$this->seat->getStartDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_('endDate')}</th>
			<td>{$this->seat->getEndDate(DATE_FORMAT)}</td>
		</tr>
		<tr><th>{$this->_(['requirement', 'requirements', 2])}</th>
			<td>$requirements</td>
		</tr>
		<tr><th>{$this->_('termLength')}</th>
            <td>{$termLength}</td>
        </tr>
    </table>
</section>
";
