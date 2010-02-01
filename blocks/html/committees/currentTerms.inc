<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
$userIsAdmin = userHasRole(array('Administrator','Clerk'));
?>
<div class="interfaceBox seats">
	<h1>Current Members</h1>
	<table>
		<thead>
			<tr><th>Name</th>
			<th>Appointed By</th>
			<th colspan="3">Term</th>
			<th colspan="2">Offices</th>
			</tr>
		</thead>
		<?php
			foreach ($this->committee->getSeats() as $seat) {
				$appointer = View::escape($seat->getAppointer());

				//--------------------------------------------------------
				// Show all the current terms
				//--------------------------------------------------------
				$terms = array();
				$c = 0;
				foreach ($seat->getCurrentTerms() as $term) {
					$c++;

					$addTermButton = '';
					$editTermButton = '';
					$deleteTermButton = '';
					$addOfficeButton = '';
					$editOfficeButton = '';
					if ($userIsAdmin) {
						$url = new URL(BASE_URL.'/terms/addTerm.php');
						$url->seat_id = $seat->getId();
						$url->person_id = $term->getPerson_id();
						$addTermButton = "
						<button type=\"button\" class=\"add\" onclick=\"document.location.href='$url';\">
						Add Term
						</button>
						";

						$url = new URL(BASE_URL.'/terms/updateTerm.php');
						$url->term_id = $term->getId();
						$editTermButton = "
						<button type=\"button\" onclick=\"document.location.href='$url';\">
						Edit Term
						</button>
						";

						if ($term->isSafeToDelete()) {
							$url = new URL(BASE_URL.'/terms/deleteTerm.php');
							$url->term_id = $term->getId();
							$url->return_url = $_SERVER['REQUEST_URI'];
							$deleteTermButton = "
							<button type=\"button\" onclick=\"document.location.href='$url';\">
						Delete Term
						</button>
							";
						}

						$url = new URL(BASE_URL.'/officers/addOfficer.php');
						$url->committee_id = $seat->getCommittee_id();
						$url->person_id = $term->getPerson_id();
						$addOfficeButton = "
						<button type=\"button\" class=\"add\" onclick=\"document.location.href='$url';\">
						Add Office
						</button>
						";
					}

					$offices = array();
					foreach ($term->getPerson()->getOffices($seat->getCommittee(),time()) as $office) {
						$editOfficeButton = '';
						if ($userIsAdmin) {
							$url = BASE_URL.'/officers/updateOfficer.php?officer_id='.$office->getId();
							$editOfficeButton = "
							<button type=\"button\" onclick=\"document.location.href='$url';\">
						Edit Office
						</button>
							";
						}
						$offices[] = View::escape($office->getTitle()).$editOfficeButton;
					}
					$offices = implode(',',$offices);

					$fullname = View::escape($term->getPerson()->getFullname());
					echo "
					<tbody><tr><td><a href=\"{$term->getPerson()->getURL()}\">$fullname</a></td>
						<td>$appointer</td>
						<td>{$term->getTerm_start('n/j/Y')} - {$term->getTerm_end('n/j/y')}</td>
						<td>$addTermButton</td>
						<td>$editTermButton</td>
						<td>$deleteTermButton</td>
						<td>$offices</td>
						<td>$addOfficeButton</td>
						<td>$editOfficeButton</td>
					</tr>
					</tbody>
					";
				}
				//--------------------------------------------------------
				// Show all the vacancies
				//--------------------------------------------------------
				for ($c; $c<$seat->getMaxCurrentTerms(); $c++) {
					$addTermButton = '';
					if ($userIsAdmin) {
						$url = new URL(BASE_URL.'/terms/addTerm.php');
						$url->seat_id = $seat->getId();
						$addTermButton = "
						<button type=\"button\" class=\"add\" onclick=\"document.location.href='$url';\">
						Add Term
						</button>
						";
					}
					echo "
					<tbody><tr><td>( Vacancy )</td>
						<td>$appointer</td>
						<td></td>
						<td>$addTermButton</td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
					";
				}
			}
		?>
	</table>
	<div>
		<?php
			$url = $this->committee->getURL();
			$url->members = 'past';
			echo "<a href=\"$url\">View Past Members</a>";
		?>
	</div>
</div>
