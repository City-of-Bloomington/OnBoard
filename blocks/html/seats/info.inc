<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Application\Models\Seat;
use Michelf\MarkdownExtra;

$seat_id       = $this->seat->getId();
$committee_id  = $this->seat->getCommittee_id();
$name          = self::escape($this->seat->getName());
$code          = self::escape($this->seat->getCode());
$appointer     = self::escape($this->seat->getAppointer());
$committee     = self::escape($this->seat->getCommittee()->getName());
$committee_url = parent::generateUri('committees.info')."?committee_id=$committee_id";
$requirements  = self::escape($this->seat->getRequirements());

if ($requirements) {
	$markdown     = new MarkdownExtra();
	$requirements = $markdown->transform($requirements);
}

$helper       = $this->template->getHelper('buttonLink');
$editButton   = '';
$endButton    = '';
$deleteButton = '';
if (Person::isAllowed('seats', 'update')) {
	$editButton = $helper->buttonLink(
		parent::generateUri('seats.update')."?seat_id=$seat_id",
		$this->_('seat_edit'),
		'edit'
	);
}
if (Person::isAllowed('seats', 'delete') && $this->seat->isSafeToDelete()) {
    $deleteButton = $helper->buttonLink(
		parent::generateUri('seats.delete')."?seat_id=$seat_id",
        $this->_('seat_delete'),
        'delete'
    );
}
$endDate = $this->seat->getEndDate('U');
if ((!$endDate || $endDate > time()) && Person::isAllowed('seats', 'end')) {
    $endButton = $helper->buttonLink(
		parent::generateUri('seats.end')."?seat_id=$seat_id",
        $this->_('seat_end'),
        'delete'
    );
}

$uri = parent::generateUri('seats.view')."?seat_id=$seat_id";
echo "
<section>
    <header>
        <h1><a href=\"$uri\">$name</a></h1>
        <div class=\"tools\">$editButton $endButton $deleteButton</div>
    </header>
    <table>
        <tr>
			<th>{$this->_('type')}</th>
            <td>{$this->_($this->seat->getType())}</td>
        </tr>
        <tr>
            <th>{$this->_('code')}</th>
            <td>$code</td>
        </tr>
		<tr>
			<th>{$this->_('appointed_by')}</th>
			<td>$appointer</td>
		</tr>
		<tr>
			<th>{$this->_('committee')}</th>
			<td><a href=\"$committee_url\">$committee</a></td>
		</tr>
";
    if ($this->seat->getStartDate()) {
        echo "
        <tr>
			<th>{$this->_('startDate')}</th>
			<td>{$this->seat->getStartDate(DATE_FORMAT)}</td>
		</tr>
        ";
    }
    if ($this->seat->getEndDate()) {
        echo "
		<tr>
			<th>{$this->_('endDate')}</th>
			<td>{$this->seat->getEndDate(DATE_FORMAT)}</td>
		</tr>
        ";
    }
    if ($requirements) {
        echo "
		<tr>
			<th>{$this->_(['requirement', 'requirements', 2])}</th>
			<td>$requirements</td>
		</tr>
		";
    }
    if ($this->seat->getType() === 'termed') {
        $termLength = Seat::$termIntervals[$this->seat->getTermLength()];
        echo "
		<tr>
			<th>{$this->_('termLength')}</th>
            <td>{$termLength}</td>
        </tr>
        ";
    }
    $voting = $this->seat->IsVoting() ? $this->_('yes') : $this->_('no');
    echo "
        <tr>
            <th>{$this->_('voting')}</th>
            <td>$voting</td>
        </tr>
    ";
    $takesApplications = $this->seat->takesApplications() ? $this->_('yes') : $this->_('no');
    echo "
        <tr><th>{$this->_('takesApplications')}</th>
            <td>$takesApplications</td>
        </tr>
    ";
echo "
    </table>
</section>
";
