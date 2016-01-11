<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$fields = [
    'type',
	'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'yearFormed', 'website',
	'email', 'phone', 'address', 'city', 'state', 'zip'
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
		$this->_('edit'),
		'edit',
		ButtonLink::SIZE_ICON
	);
}

echo "
<div>
	<h2>$name
		$editButton
	</h2>

	<table>
        <tr><th>{$this->_('type')}</th>
            <td>$type</td>
        </tr>
		<tr><th>{$this->_('year_formed')}</th>
			<td>$yearFormed</td>
		</tr>
		<tr><th>{$this->_('statutoryName')}</th>
			<td>$statutoryName</td>
		</tr>
		<tr><th>{$this->_('statuteReference')}</th>
			<td>$statuteReference</td>
		</tr>
		<tr><th>{$this->_('website')}</th>
			<td>$website</td>
		</tr>
		<tr><th>{$this->_(['email', 'emails', 1])}</th>
            <td>$email</td>
        </tr>
        <tr><th>{$this->_('phone')}</th>
            <td>$phone</td>
        </tr>
        <tr><th>{$this->_('address')}</th>
            <td><address>$address
                $city $state $zip
                </address>
            </td>
	</table>

    $description

    <h3>{$this->_('meetingSchedule')}</h3>
    $meetingSchedule

    <h3>{$this->_('contactInfo')}</h3>
    $contactInfo
</div>
";
