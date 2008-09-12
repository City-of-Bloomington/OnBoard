<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$name = View::escape($this->committee->getName());
$addSeatButton = '';
if (userHasRole('Administrator'))
{
	$addSeatButton = "<a class=\"add button\" href=\"".BASE_URL."/committees/addSeat.php?committee_id={$this->committee->getId()}\">Add</a>";
}
echo "
<h1>$name</h1>
<div class=\"interfaceBox\">
	<h1>$addSeatButton Seats</h1>
";
if (count($this->committee->getSeats()))
{
	echo "
	<table>
	<tr><td>Title</td>
		<td>Member</td>
		<td>Appointed By</td>
		<td>Requirements</td>
	</tr>
	";
	foreach($this->committee->getSeats() as $seat)
	{
		$title = View::escape($seat->getTitle());
		$appointment = View::escape($seat->getAppointer());
		$requirements = View::escape($seat->getRequirements());
		echo "
		";
	}
}