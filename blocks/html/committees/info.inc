<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;
use Michelf\MarkdownExtra;

$name             = View::escape($this->committee->getName());
$description      = View::escape($this->committee->getDescription());
$statutoryName    = View::escape($this->committee->getStatutoryName());
$statuteReference = View::escape($this->committee->getStatuteReference());

$markdown = new MarkdownExtra();
$description = $markdown->transform($this->committee->getDescription());

echo "
<div>
	<h2><a href=\"{$this->committee->getUri()}\">$name</a></h2>

	<table>
		<tr><th>Date Formed</th>
			<td>{$this->committee->getDateFormed(DATE_FORMAT)}</td>
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
