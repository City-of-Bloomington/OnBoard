<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param SeatList $this->seatList
 * @param Committee $this->committee (optional)
 */
$addSeatButton = '';
if (isset($this->committee) && userHasRole(array('Administrator','Clerk')))
{
	$url = new URL(BASE_URL.'/seats/addSeat.php');
	$url->committee_id = $this->committee->getId();
	$addSeatButton = "<a class=\"add button\" href=\"$url\">Add</a>";
}
?>
<div class="interfaceBox">
	<h1><?php echo $addSeatButton; ?>
		Seats
	</h1>
	<table>
	<tr><td></td>
		<td>Member</td>
		<td>Title</td>
		<td>Appointed By</td>
	</tr>
	<?php
		foreach($this->seatList as $seat)
		{
			$editButton = '';
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/seats/updateSeat.php');
				$url->seat_id = $seat->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}

			$title = View::escape($seat->getTitle());
			$appointer = View::escape($seat->getAppointer());

			$member = $seat->getMember();
			$memberName = $member ? View::escape($member->getFirstname().' '.$member->getLastname()) : 'Vacant';
			echo "
			<tr><td>$editButton</td>
				<td>$memberName</td>
				<td><a href=\"".BASE_URL."/seats/viewSeat.php?seat_id={$seat->getId()}\">$title</a></td>
				<td>$appointer</td>
			</tr>
			";
		}
	?>
	</table>
</div>
