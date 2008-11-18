<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
$userIsAdmin = userHasRole(array('Administrator','Clerk'));
$addSeatButton = '';
if (isset($this->committee) && $userIsAdmin)
{
	$url = new URL(BASE_URL.'/seats/addSeat.php');
	$url->committee_id = $this->committee->getId();
	$addSeatButton = "<a class=\"add button\" href=\"$url\">Add Seat</a>";
}
?>
<div class="interfaceBox">
	<h1><?php echo $addSeatButton; ?>
		Seats
	</h1>
	<table>
	<tr><th></th>
		<th>Title</th>
		<th>Appointed By</th>
		<th>Term</th>
	</tr>
	<?php
		foreach($this->committee->getSeats() as $seat)
		{
			$editButton = '';
			if ($userIsAdmin)
			{
				$url = new URL(BASE_URL.'/seats/updateSeat.php');
				$url->seat_id = $seat->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit Seat</a>";
			}
			$title = View::escape($seat->getTitle());
			$appointer = View::escape($seat->getAppointer());

			#--------------------------------------------------------
			# Show all the members
			#--------------------------------------------------------
			$members = array();
			$c = 0;
			foreach($seat->getCurrentMembers() as $member)
			{
				$c++;
				$fullname = View::escape($member->getFullname());
				echo "
				<tr><td><a href=\"{$member->getURL()}\">$fullname</a></td>
					<td>$editButton
						<a href=\"{$seat->getURL()}\">$title</a>
					</td>
					<td>$appointer</td>
					<td>{$member->getTerm_start('n/j/Y')} - {$member->getTerm_end('n/j/y')}</td>
				</tr>
				";
			}
			#--------------------------------------------------------
			# Show all the vacancies
			#--------------------------------------------------------
			for($c; $c<$seat->getMaxCurrentMembers(); $c++)
			{
				echo "
				<tr><td>( Vacancy )</td>
					<td>$editButton
						<a href=\"{$seat->getURL()}\">$title</a>
					</td>
					<td>$appointer</td>
					<td></td>
				</tr>
				";
			}
		}
	?>
	</table>
</div>
