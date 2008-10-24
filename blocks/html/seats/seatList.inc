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
	<tr><th></th>
		<th>Title</th>
		<th>Max Current Members</th>
		<th>Appointed By</th>
		<th>Current Members</th>
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

			$members = array();
			foreach($seat->getCurrentMembers() as $member)
			{
				$fullname = View::escape($member->getFullname());
				$members[] = "<a href=\"{$member->getURL()}\">$fullname</a>";
			}
			$members = implode(', ',$members);

			$vacancy = (count($seat->getCurrentMembers()) < $seat->getMaxCurrentMembers()) ? '(Vacancy)' : '';
			echo "
			<tr><td>$editButton</td>
				<td><a href=\"{$seat->getURL()}\">$title</a></td>
				<td>{$seat->getMaxCurrentMembers()}</td>
				<td>$appointer</td>
				<td>$members $vacancy</td>
			</tr>
			";
		}
	?>
	</table>
</div>
