<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
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
        <dl class=\"fn1-output-field\">
			<dt>{$this->_('type')}</dt>
            <dd>{$this->_($this->seat->getType())}</dd>
        </dl>
        <dl class=\"fn1-output-field\">
            <dt>{$this->_('code')}</dt>
            <dd>$code</dd>
        </dl>
		<dl class=\"fn1-output-field\">
			<dt>{$this->_('appointed_by')}</dt>
			<dd>$appointer</dd>
		</dl>
		<dl class=\"fn1-output-field\">
			<dt>{$this->_(['committee', 'committees', 1])}</dt>
			<dd><a href=\"$committee_url\">$committee</a></dd>
		</dl>
";
    if ($this->seat->getStartDate()) {
        echo "
        <dl class=\"fn1-output-field\">
			<dt>{$this->_('startDate')}</dt>
			<dd>{$this->seat->getStartDate(DATE_FORMAT)}</dd>
		</dl>
        ";
    }
    if ($this->seat->getEndDate()) {
        echo "
		<dl class=\"fn1-output-field\">
			<dt>{$this->_('endDate')}</dt>
			<dd>{$this->seat->getEndDate(DATE_FORMAT)}</dd>
		</dl>
        ";
    }
    if ($requirements) {
        echo "
		<dl class=\"fn1-output-field\">
			<dt>{$this->_(['requirement', 'requirements', 2])}</dt>
			<dd>$requirements</dd>
		</dl>
		";
    }
    if ($this->seat->getType() === 'termed') {
        $termLength = Seat::$termIntervals[$this->seat->getTermLength()];
        echo "
		<dl class=\"fn1-output-field\">
			<dt>{$this->_('termLength')}</dt>
            <dd>{$termLength}</dd>
        </dl>
        ";
    }
    $voting = $this->seat->IsVoting() ? $this->_('yes') : $this->_('no');
    echo "
        <dl class=\"fn1-output-field\">
            <dt>{$this->_('voting')}</dt>
            <dd>$voting</dd>
        </dl>
    ";
echo "
</section>
";
