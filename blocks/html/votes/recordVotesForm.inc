<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
use Application\Models\VotingRecord;
use Blossom\Classes\View;
?>
<h2><?php echo $this->_('labels.edit_votingRecords'); ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/votes/recordVotes">
	<fieldset><legend><?php echo $this->_(['labels.votingRecord', 'labels.votingRecords', 1]); ?></legend>
		<input name="vote_id" type="hidden" value="<?php echo $this->vote->getId(); ?>" />

		<table>
			<thead>
			<tr><th><?php echo $this->_(['labels.member', 'labels.members', 1]); ?></th>
				<th><?php echo $this->_(['labels.term',   'labels.terms',   1]); ?></th>
				<?php
					foreach (VotingRecord::$positions as $position) {
						echo '<th>'.ucfirst($position).'</td>';
					}
				?>
			</tr>
			</thead>
			<tbody>
			<?php
				// Sort the terms by lastname
				$terms = [];
				foreach ($this->vote->getTerms() as $term) {
					$key = $term->getPerson()->getLastname().'-'.$term->getId();
					$terms[$key] = $term;
				}
				ksort($terms);

				foreach ($terms as $term) {
					$fullname = View::escape($term->getPerson()->getFullname());
					$votingRecord = $this->vote->getVotingRecord($term);
					$termStart = $term->getTerm_start(DATE_FORMAT);
					$termEnd   = $term->getTerm_end  (DATE_FORMAT);

					echo "
					<tr><th>$fullname</th>
						<th>$termStart - $termEnd</th>
					";
					foreach (VotingRecord::$positions as $position) {
						$checked = $position.'Checked';
						$$checked = $votingRecord->getPosition()==$position ? ' checked="checked"' : '';
						echo "
						<td><input name=\"votingRecords[{$term->getId()}]\" type=\"radio\"
								value=\"$position\" {$$checked} />
						</td>
						";
					}
					echo "
					</tr>
					";
				}
			?>
			</tbody>
		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($this->vote->getTopic()->getUri());
		?>
	</fieldset>
</form>
