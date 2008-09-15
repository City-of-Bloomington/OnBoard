<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
$title = View::escape($this->seat->getTitle());
$appointer = View::escape($this->seat->getAppointer());
$requirements = View::escape($this->seat->getRequirements());
$committee = View::escape($this->seat->getCommittee()->getName());
echo "
<div class=\"interfaceBox\">
	<h1>$title</h1>
	<table>
	<tr><th>Appointed By</th>
		<td>$appointer</td>
	</tr>
	<tr><th>Requirements</th>
		<td>$requirements</td>
	</tr>
	<tr><th>Committee</th>
		<td>$committee</td>
	</tr>
	</table>
</div>
";