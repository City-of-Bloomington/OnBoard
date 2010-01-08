<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CommitteeList $this->committeeList
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			$userIsAdmin = userHasRole(array('Administrator','Clerk'));
			if ($userIsAdmin) {
				$url = new URL(BASE_URL.'/committees/addCommittee.php');
				$addURL->return_url = $return_url;
				echo "
				<button type=\"button\" class=\"add\" onclick=\"document.location.href='$addURL';\">
				Add
				</button>
				";
			}
		?>
		Councils, Commissions, Committees & Boards
	</h1>
	<table>
	<thead>
		<tr><th></th>
			<th>Name</th>
			<th>Seats</th>
		</tr>
	</thead>
		<?php
			foreach ($this->committeeList as $committee) {
				$name = View::escape($committee->getName());
				$seats = $committee->getMaxCurrentTerms();

				$editButton = '';
				if ($userIsAdmin) {
					$editURL = new URL(BASE_URL.'/committees/updateCommittee.php');
					$editURL->committee_id = $committee->getId();
					$editURL->return_url = $return_url;
					$editButton = "
					<button type=\"button\" class=\"edit\" onclick=\"document.location.href='$editURL';\">
						Edit
					</button>
					";
				}

				$url = new URL(BASE_URL.'/committees/viewCommittee.php');
				$url->committee_id = $committee->getId();
				echo "
	<tbody>
		<tr><td>$editButton</td>
			<td><a href=\"$url\">$name</a></td>
			<td>$seats</td>
		</tr>
				";
			}
		?>
	</body>
	</table>
</div>
