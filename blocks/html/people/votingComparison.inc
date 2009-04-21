<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Person $this->person
 */
$firstname = View::escape($this->person->getFirstname());
?>
<div class="interfaceBox memberVoteComparison">
	<h1>Voting Comparison</h1>
	<p>Shows how often (as a percentage) that other members vote the same
		way as <?php echo $firstname; ?>
	</p>
	<table>
		<tr><th>Member</th>
			<th>All Votes</th>
			<?php
				$finalVoteType = VoteType::getFinalVoteType();
				echo '<th>'.View::escape($finalVoteType).' Votes</th>';

				$topicTypes = new TopicTypeList();
				$topicTypes->find();
				foreach ($topicTypes as $topicType) {
					$name = View::escape($topicType);
					echo "<th>$name</th>";
				}
			?>
		</tr>
		<?php
			// Gather all the data for this page
			$comparison = array();
			$peers = $this->person->getPeers();
			foreach ($peers as $peer) {
				$data = array('firstname'=>$peer->getFirstname(),
								'lastname'=>$peer->getLastname(),
								  'fullname'=>$peer->getFullname(),
								  'url'=>$peer->getURL());
				$data['allVotes'] = $this->person->getAccordancePercentage($peer);
				$data['finalVotes'] = $this->person->getAccordancePercentage($peer,
																			 null,
																			 $finalVoteType);
				foreach ($topicTypes as $topicType) {
					$data[$topicType->getName()] = $this->person->getAccordancePercentage($peer,
																						  $topicType->getTopics());
				}
				$comparison[] = $data;
			}

			foreach ($comparison as $row) {
				$fullname = View::escape($row['fullname']);
				echo "
				<tr><td><a href=\"$row[url]\">$fullname</a></td>
					<td>$row[allVotes]</td>
					<td>$row[finalVotes]</td>
				";
				foreach ($topicTypes as $topicType) {
					echo "<td>".$row[$topicType->getName()]."</td>";
				}
				echo "
				</tr>
				";
			}
		?>
	</table>
</div>
