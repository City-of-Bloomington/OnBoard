<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
?>
<h1>Add/Edit Voting Records</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Voting Record Info</legend>
		<input name="vote_id" type="hidden" value="<?php echo $this->vote->getId(); ?>" />

		<table>
			<tr><th>Member</th>
				<?php
					$positions = VotingRecord::getPossiblePositions();
					foreach ($positions as $position) {
						echo '<th>'.ucfirst($position).'</td>';
					}
				?>
			</tr>
			<?php
				foreach ($this->vote->getTerms() as $term) {
					$fullname = View::escape($term->getPerson()->getFullname());
					$votingRecord = $this->vote->getVotingRecord($term);

					echo "
					<tr><th>$fullname</th>
					";
					foreach ($positions as $position) {
						$checked = $position.'Checked';
						$$checked = $votingRecord->getPosition()==$position ? ' checked="checked"' : '';
						echo "
						<td><input name=\"votingRecord[{$term->getId()}]\"
									type=\"radio\"
									value=\"$position\"
									{$$checked} />
						</td>
						";
					}
					echo "
					</tr>
					";
				}
			?>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->vote->getTopic()->getURL(); ?>';">
			Cancel
		</button>
	</fieldset>
</form>
