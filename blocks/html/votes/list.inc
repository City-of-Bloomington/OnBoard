<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->votes
 * @param Topic $this->topic (optional)
 * @param string $this->title (optional)
 */
use Application\Models\Person;
use Application\Models\VotingRecord;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;

$title = isset($this->title) ? View::escape($this->title) : $this->_(['labels.vote', 'labels.votes', 2]);
$userCanEditVotes = Person::isAllowed('votes', 'edit');
$helper = $this->template->getHelper('buttonLink');
?>
<div>
	<h2><?php
			echo $title;

			if (isset($this->topic) && $userCanEditVotes) {
				echo $helper->buttonLink(
					BASE_URI.'/votes/update?topic_id='.$this->topic->getId(),
					$this->translate('labels.add_vote'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_(['labels.vote',  'labels.votes',  1]); ?></th>
				<th><?php echo $this->_(['labels.topic', 'labels.topics', 1]); ?></th>
				<th><?php echo $this->_('labels.date'); ?></th>
				<?php
					foreach (VotingRecord::$positions as $position) {
						$position = ucfirst($position);
						echo "<th>$position</th>";
					}
				?>
				<th><?php echo $this->_('labels.outcome'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->votes as $vote) {
				$editButton = '';
				if ($userCanEditVotes) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/votes/update?vote_id='.$vote->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				$number = View::escape($vote->getTopic()->getNumber());

				echo "
				<tr><th>$editButton</th>
					<th><a href=\"{$vote->getUri()}\">{$vote->getVoteType()}</a></th>
					<th><a href=\"{$vote->getTopic()->getUri()}\">$number</a></th>
					<td>{$vote->getDate(DATE_FORMAT)}</td>
				";
				foreach (VotingRecord::$positions as $position) {
					echo '<td>'.count($vote->getVotingRecords($position)).'</td>';
				}
				echo "
					<td>{$vote->getOutcome()}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
