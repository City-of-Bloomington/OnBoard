<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Application\Models\Seat;
use Michelf\MarkdownExtra;

$name          = self::escape($this->seat->getName());
$code          = self::escape($this->seat->getCode());
$appointer     = self::escape($this->seat->getAppointer());
$committee     = self::escape($this->seat->getCommittee()->getName());
$committee_url = BASE_URI.'/committees/info?committee_id='.$this->seat->getCommittee_id();
$requirements  = self::escape($this->seat->getRequirements());

if ($requirements) {
	$markdown = new MarkdownExtra();
	$requirements = $markdown->transform($requirements);
}

$helper = $this->template->getHelper('buttonLink');
$editButton   = '';
$endButton    = '';
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
$endDate = $this->seat->getEndDate('U');
if ((!$endDate || $endDate > time()) && Person::isAllowed('seats', 'end')) {
    $endButton = $helper->buttonLink(
        BASE_URI.'/seats/end?seat_id='.$this->seat->getId(),
        $this->_('seat_end'),
        'delete'
    );
}

$uri = BASE_URI.'/seats/view?seat_id='.$this->seat->getId();
echo "
<section>
    <header>
        <h1><a href=\"$uri\">$name</a></h1>
        <div class=\"tools\">$editButton $endButton $deleteButton</div>
    </header>
        <dl>
			<dt>{$this->_('type')}</dt>
            <dd>{$this->_($this->seat->getType())}</dd>
        </dl>
        <dl>
            <dt>{$this->_('code')}</dt>
            <dd>$code</dd>
        </dl>
		<dl>
			<dt>{$this->_('appointed_by')}</dt>
			<dd>$appointer</dd>
		</dl>
		<dl>
			<dt>{$this->_('committee')}</dt>
			<dd><a href=\"$committee_url\">$committee</a></dd>
		</dl>
";
    if ($this->seat->getStartDate()) {
        echo "
        <dl>
			<dt>{$this->_('startDate')}</dt>
			<dd>{$this->seat->getStartDate(DATE_FORMAT)}</dd>
		</dl>
        ";
    }
    if ($this->seat->getEndDate()) {
        echo "
		<dl>
			<dt>{$this->_('endDate')}</dt>
			<dd>{$this->seat->getEndDate(DATE_FORMAT)}</dd>
		</dl>
        ";
    }
    if ($requirements) {
        echo "
		<dl>
			<dt>{$this->_(['requirement', 'requirements', 2])}</dt>
			<dd>$requirements</dd>
		</dl>
		";
    }
    if ($this->seat->getType() === 'termed') {
        $termLength = Seat::$termIntervals[$this->seat->getTermLength()];
        echo "
		<dl>
			<dt>{$this->_('termLength')}</dt>
            <dd>{$termLength}</dd>
        </dl>
        ";
    }
    $voting = $this->seat->IsVoting() ? $this->_('yes') : $this->_('no');
    echo "
        <dl>
            <dt>{$this->_('voting')}</dt>
            <dd>$voting</dd>
        </dl>
    ";
echo "
</section>
";
