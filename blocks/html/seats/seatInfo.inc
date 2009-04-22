<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
$name = View::escape($this->seat->getName());
$appointer = View::escape($this->seat->getAppointer());
$committee = View::escape($this->seat->getCommittee()->getName());


$addTermButton = '';
$editSeatButton = '';
$editRequirementsButton = '';
if (userHasRole(array('Administrator','Clerk'))) {
	$url = BASE_URL.'/terms/addTerm.php?seat_id='.$this->seat->getId();
	$addTermButton = "
	<button type=\"button\" onclick=\"document.location.href='$url';\">
		Add Term
	</button>
	";

	$url = BASE_URL.'/seats/updateSeat.php?seat_id='.$this->seat->getId();
	$editSeatButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

	$url = new URL(BASE_URL.'/seats/editRequirements.php');
	$url->seat_id = $this->seat->getId();
	$editRequirementsButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
}
echo "
<div class=\"interfaceBox\">
	<h1>$editSeatButton$name$addTermButton</h1>
	<table>
		<tr><th>Appointed By</th>
			<td>$appointer</td>
		</tr>
		<tr><th>Committee</th>
			<td>$committee</td>
		</tr>
		<tr><th>Maximum Current Terms</th>
			<td>{$this->seat->getMaxCurrentTerms()}</td>
		</tr>
		<tr><th>$editRequirementsButton
				Requirements
			</th>
			<td>
";
				if ($this->seat->hasRequirements()) {
					echo '<ul>';
					foreach ($this->seat->getRequirements() as $requirement) {
						echo '<li>'.View::escape($requirement).'</li>';
					}
					echo '</ul>';
				}
echo "
			</td>
		</tr>
	</table>
</div>
";
