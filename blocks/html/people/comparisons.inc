<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\TopicTypeTable;
use Application\Models\VoteType;
use Blossom\Classes\View;

$firstname = View::escape($this->person->getFirstname());
?>
<div>
	<p><?php
			echo sprintf($this->_('messages.votingRecordComparison_explanation'), $firstname);
		?>
	</p>
	<table>
		<thead>
			<tr><th><?php echo $this->_(['labels.member', 'labels.members', 1]); ?></th>
					<?php
						$finalVoteType = VoteType::getFinalVoteType();
						echo "<th>$finalVoteType {$this->_(['labels.vote', 'labels.votes', 2])}</th>";

						$topicTypes = [];
						$table = new TopicTypeTable();
						$list = $table->find();
						foreach ($list as $t) {
							$name = View::escape($t);
							echo "<th>$name</th>";
							$topicTypes[] = $t;
						}
					?>
				<th><?php echo $this->_('labels.all_votes'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			// Gather all the data for this page
			$comparison = [];
			$peers = $this->person->getPeers();
			foreach ($peers as $peer) {
				$data = [
					'firstname' =>$peer->getFirstname(),
					'lastname'  =>$peer->getLastname(),
					'fullname'  =>$peer->getFullname(),
					'uri'       =>$peer->getUri(),
					'allVotes'  =>$this->person->getAccordancePercentage($peer),
					'finalVotes'=>$this->person->getAccordancePercentage($peer, null, $finalVoteType)
				];
				foreach ($topicTypes as $type) {
					$ids = [];
					foreach ($type->getTopics() as $topic) {
						$ids[] = $topic->getId();
					}
					$data[$type->getName()] = $this->person->getAccordancePercentage($peer, $ids);
				}
				$comparison[] = $data;
			}

			foreach ($comparison as $row) {
				$fullname = View::escape($row['fullname']);
				echo "
				<tr><td><a href=\"$row[uri]\">$fullname</a></td>
					<td>$row[finalVotes]</td>
				";
				foreach ($topicTypes as $t) {
					echo "<td>{$row[$t->getName()]}</td>";
				}
				echo "
					<td>$row[allVotes]</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
