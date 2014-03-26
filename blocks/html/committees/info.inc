<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$name             = View::escape($this->committee->getName());
$description      = View::escape($this->committee->getDescription());
$statutoryName    = View::escape($this->committee->getStatutoryName());
$statuteReference = View::escape($this->committee->getStatuteReference());

$markdown = new MarkdownExtra();
$description = $markdown->transform($this->committee->getDescription());

$editButton = '';
if (Person::isAllowed('committees', 'edit')) {
	$editButton = sprintf(
		'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
		BASE_URI.'/committees/update?committee_id='.$this->committee->getId(),
		$this->_('labels.edit')
	);
}

echo "
<div>
	<h2><a href=\"{$this->committee->getUri()}\">$name</a>
		$editButton
	</h2>

	<table>
		<tr><th>Year Formed</th>
			<td>{$this->committee->getYearFormed()}</td>
		</tr>
		<tr><th>Statutory Name</th>
			<td>$statutoryName</td>
		</tr>
		<tr><th>Statute Reference</th>
			<td>$statuteReference</td>
		</tr>
		<tr><th>Website</th>
			<td><a href=\"{$this->committee->getWebsite()}\">{$this->committee->getWebsite()}</a></td>
		</tr>
	</table>
	$description
</div>
";
