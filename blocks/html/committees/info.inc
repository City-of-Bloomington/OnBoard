<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$fields = [
	'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'yearFormed', 'website'
];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = View::escape($this->committee->$get());
}

if ($statuteUrl) {
	$statuteReference = "<a href=\"$statuteUrl\">$statuteReference</a>";
}
if ($website) {
	$website = "<a href=\"$website\">$website</a>";
}

$markdown = new MarkdownExtra();
$description     = $markdown->transform($this->committee->getDescription());
$contactInfo     = $markdown->transform($this->committee->getContactInfo());
$meetingSchedule = $markdown->transform($this->committee->getMeetingSchedule());

$editButton = '';
if (Person::isAllowed('committees', 'edit')) {
	$helper = $this->template->getHelper('buttonLink');
	$editButton = $helper->buttonLink(
		BASE_URI.'/committees/update?committee_id='.$this->committee->getId(),
		$this->_('labels.edit'),
		'edit',
		ButtonLink::SIZE_ICON
	);
}

echo "
<div>
	<h2><a href=\"{$this->committee->getUri()}\">$name</a>
		$editButton
	</h2>

	<table>
		<tr><th>{$this->_('labels.year_formed')}</th>
			<td>$yearFormed</td>
		</tr>
		<tr><th>{$this->_('labels.statutoryName')}</th>
			<td>$statutoryName</td>
		</tr>
		<tr><th>{$this->_('labels.statuteReference')}</th>
			<td>$statuteReference</td>
		</tr>
		<tr><th>{$this->_('labels.website')}</th>
			<td>$website</td>
		</tr>
	</table>

    $description

    <h3>{$this->_('labels.meetingSchedule')}</h3>
    $meetingSchedule

    <h3>{$this->_('labels.contactInfo')}</h3>
    $contactInfo
</div>
";
