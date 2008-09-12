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
	$url = new URL(BASE_URL.'/seats/addSeat.php');
	$url->committee_id = $this->committee->getId();
	$addSeatButton = "<a class=\"add button\" href=\"$url\">Add</a>";
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
	<tr><td></td>
		<td>Member</td>
		<td>Title</td>
		<td>Appointed By</td>
		<td>Requirements</td>
	</tr>
	";
	foreach($this->committee->getSeats() as $seat)
	{
		$editButton = '';
		if (userHasRole('Administrator'))
		{
			$url = new URL(BASE_URL.'/seats/updateSeat.php');
			$url->seat_id = $seat->getId();
			$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
		}

		$title = View::escape($seat->getTitle());
		$appointer = View::escape($seat->getAppointer());
		$requirements = View::escape($seat->getRequirements());
		$member = $seat->getMember() ? View::escape($seat->getMember()->getFullName()) : 'Vacant';
		echo "
		<tr><td>$editButton</td>
			<td>$member</td>
			<td>$title</td>
			<td>$appointer</td>
			<td>$requirements</td>
		</tr>
		";
	}
	echo "</table>";
}
echo "
</div>
";
