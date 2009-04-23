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
		<tr><th>Name</th>
			<th>Appointed By</th>
			<th>Term</th>
			<th></th>
			<th>Offices</th>
			<th></th>
		</tr>
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
					if ($userIsAdmin) {
						$url = new URL(BASE_URL.'/terms/addTerm.php');
						$url->seat_id = $seat->getId();
						$url->person_id = $term->getPerson_id();
						$addTermButton = "
						<a class=\"add button\" href=\"$url\" title=\"Add Term\">Add</a>
						";

						$url = new URL(BASE_URL.'/terms/updateTerm.php');
						$url->term_id = $term->getId();
						$editTermButton = "
						<a class=\"edit button\" href=\"$url\" title=\"Edit Term\">Edit</a>
						";

						if ($term->isSafeToDelete()) {
							$url = new URL(BASE_URL.'/terms/deleteTerm.php');
							$url->term_id = $term->getId();
							$url->return_url = $_SERVER['REQUEST_URI'];
							$deleteTermButton = "
							<a class=\"delete button\" href=\"$url\" title=\"Delete Term\">Delete</a>
							";
						}

						$url = new URL(BASE_URL.'/officers/addOfficer.php');
						$url->committee_id = $seat->getCommittee_id();
						$url->person_id = $term->getPerson_id();
						$addOfficeButton = "
						<a class=\"add button\" href=\"$url\" title=\"Add Office\">Add</a>
						";
					}

					$offices = array();
					foreach ($term->getPerson()->getOffices($seat->getCommittee(),time()) as $office) {
						$editOfficeButton = '';
						if ($userIsAdmin) {
							$url = BASE_URL.'/officers/updateOfficer.php?officer_id='.$office->getId();
							$editOfficeButton = "
							<a class=\"edit button\" href=\"$url\" title=\"Edit Office\">Edit</a>
							";
						}
						$offices[] = View::escape($office->getTitle()).$editOfficeButton;
					}
					$offices = implode(',',$offices);

					$fullname = View::escape($term->getPerson()->getFullname());
					echo "
					<tr><td><a href=\"{$term->getPerson()->getURL()}\">$fullname</a></td>
						<td>$appointer</td>
						<td>{$term->getTerm_start('n/j/Y')} - {$term->getTerm_end('n/j/y')}</td>
						<td>$addTermButton $editTermButton $deleteTermButton</td>
						<td>$offices</td>
						<td>$addOfficeButton</td>
					</tr>
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
						<a class=\"add button\" href=\"$url\" title=\"Add Term\">Add</a>
						";
					}
					echo "
					<tr><td>( Vacancy )</td>
						<td>$appointer</td>
						<td></td>
						<td>$addTermButton</td>
						<td></td>
						<td></td>
					</tr>
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
