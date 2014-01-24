<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
use Application\Models\VoteTypeTable;
use Blossom\Classes\View;

$title = $this->vote->getId() ? $this->_('labels.edit_vote') : $this->_('labels.add_vote');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/votes/update">
		<fieldset><legend><?php echo $this->_('labels.info_vote'); ?></legend>
			<?php
				if ($this->vote->getId()) {
					echo "<input name=\"vote_id\" type=\"hidden\" value=\"{$this->vote->getId()}\" />";
				}
				else {
					echo "<input name=\"topic_id\" type=\"hidden\" value=\"{$this->vote->getTopic_id()}\" />";
				}
			?>

			<table>
				<tr><td><label for="date"><?php echo $this->_('labels.date'); ?></label></td><td>
						<input name="date" id="date" size="10" maxlength="10"
								value="<?php echo $this->vote->getDate(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr><td><label for="voteType_id"><?php echo $this->_(['labels.voteType', 'labels.voteTypes', 1]); ?></label></td>
					<td><select name="voteType_id" id="voteType_id">
						<?php
							$table = new VoteTypeTable();
							$types = $table->find();
							foreach ($types as $type) {
								$name = View::escape($type);
								$selected = $this->vote->getVoteType_id()==$type->getId() ? ' selected="selected"' : '';
								echo "<option value=\"{$type->getId()}\"$selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td><label for='outcome'><?php echo $this->_('labels.outcome'); ?></td>
					<td><select name='outcome' id='outcome'>
							<option></option>
							<option<?php echo $this->vote->getOutcome()=='pass' ? ' selected="selected"' : ''; ?>>pass</option>
							<option<?php echo $this->vote->getOutcome()=='fail' ? ' selected="selected"' : ''; ?>>fail</option>
						</select>
					</td>
				</tr>
			</table>

			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->vote->getTopic()->getUri());
			?>
		</fieldset>
	</form>
</div>
