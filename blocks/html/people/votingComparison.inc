<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Person $this->person
 */
?>
<div class="interfaceBox memberVoteComparison">
	<h1>Voting Comparison</h1>
	<table>
		<tr><th>Member</th><th>Percentage</th></tr>
		<?php
			// Gather all the data for this page
			$comparison = array();
			$peers = $this->person->getPeers();
			foreach ($peers as $peer) {
				$comparison[] = array('firstname'=>$peer->getFirstname(),
									  'lastname'=>$peer->getLastname(),
									  'fullname'=>$peer->getFullname(),
									  'url'=>$peer->getURL(),
									  'percentage'=>$this->person->getAccordancePercentage($peer));
			}

			// Sort the result by percentage, lastname, firstname
			foreach ($comparison as $key=>$row) {
				$percentage[$key] = $row['percentage'];
				$lastname[$key] = $row['lastname'];
				$firstname[$key] = $row['firstname'];
			}
			array_multisort($percentage,SORT_DESC,
							$lastname,SORT_STRING,
							$firstname,SORT_STRING,
							$comparison);

			// Output the sorted results
			foreach ($comparison as $row) {
				$fullname = View::escape($row['fullname']);
				echo "
				<tr><td><a href=\"$row[url]\">$fullname</a></td>
					<td>$row[percentage]</td>
				</tr>
				";
			}
		?>
	</table>
</div>
