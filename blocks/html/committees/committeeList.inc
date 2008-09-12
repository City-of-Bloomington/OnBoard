<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole('Administrator'))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/committees/addCommittee.php\">Add</a>";
			}
		?>
		Committees
	</h1>
	<table>
	<tr><th></th>
		<th>Name</th>
		<th>Seats</th>
		<th>Vacancy?</th>
	</tr>
	<?php
		foreach($this->committeeList as $committee)
		{
			$name = View::escape($committee->getName());
			$seats = count($committee->getSeats());
			$editButton = '';
			if (userHasRole('Administrator'))
			{
				$editButton = "<a class=\"edit button\" href=\"".BASE_URL."/committees/updateCommittee.php?committee_id={$committee->getId()}\">Edit</a>";
			}
			echo "
			<tr><td>$editButton</td>
				<td>$name</td>
				<td>$seats</td>
				<td></td>
			</tr>
			";
		}
	?>
	</table>
</div>
