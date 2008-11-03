<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$name = View::escape($this->committee->getName());
$statutoryName = View::escape($this->committee->getStatutoryName());
$statuteReference = View::escape($this->committee->getStatuteReference());
$description = View::escape($this->committee->getDescription());

echo "
<h1><a href=\"{$this->committee->getURL()}\">$name</a></h1>
<p>$description</p>
<table>
<tr><th>Date Formed</th>
	<td>{$this->committee->getDateFormed('n/j/Y')}</td></tr>
<tr><th>Statutory Name</th>
	<td>$statutoryName</td></tr>
<tr><th>Statute Reference</th>
	<td>$statuteReference</td></tr>
<tr><th>Website</th>
	<td><a href=\"{$this->committee->getWebsite()}\">{$this->committee->getWebsite()}</a></td></tr>
</table>
";
