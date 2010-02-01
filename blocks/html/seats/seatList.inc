<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SeatList $this->seatList
 * @param Committee $this->committee (optional)
 */
$userCanEdit = userHasRole(array('Administrator','Clerk'));
?>
<div class="interfaceBox">
	<h1><?php
			if (isset($this->committee) && $userCanEdit) {
				$url = new URL(BASE_URL.'/seats/addSeat.php');
				$url->committee_id = $this->committee->getId();
				echo "<button type=\"button\" class=\"add\" onclick=\"document.location.href='url';\">
				Add Seat
				</button>";
			}
		?>
		Seats
	</h1>
	<table>
		<tr><th>Name</th>
			<th>Appointer</th>
			<th>Max Concurrent Terms</th>
		</tr>
		<?php
			foreach ($this->seatList as $seat) {
				$name = View::escape($seat->getName());
				$appointer = View::escape($seat->getAppointer());
				echo "
				<tr><td><a href=\"{$seat->getURL()}\">$name</a></td>
					<td>$appointer</td>
					<td>{$seat->getMaxCurrentTerms()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
