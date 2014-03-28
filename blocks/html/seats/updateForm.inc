<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\AppointerTable;
use Blossom\Classes\View;

$title = $this->seat->getId() ? $this->_('labels.edit_seat') : $this->_('labels.add_seat');
?>
<h2><?php echo $title; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/seats/update">
	<fieldset><legend><?php echo $this->_('labels.info_seat'); ?></legend>
		<?php
			if ($this->seat->getId()) {
				echo "<input name=\"seat_id\" type=\"hidden\" value=\"{$this->seat->getId()}\" />";
			}
			else {
				echo "<input name=\"committee_id\" type=\"hidden\" value=\"{$this->seat->getCommittee_id()}\" />";
			}
		?>
		<table>
			<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
				<td><input name="name" id="name"
						value="<?php echo View::escape($this->seat->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="appointer_id" class="required">
						<?php echo $this->_(['labels.appointer', 'labels.appointers', 1]); ?>
					</label>
				</td>
				<td><select name="appointer_id" id="appointer_id"><option value=""></option>
					<?php
						$table = new AppointerTable();
						$list = $table->find();
						foreach ($list as $appointer) {
							$name = View::escape($appointer->getName());
							$selected = ($this->seat->getAppointer_id()==$appointer->getId())
										? ' selected="selected"'
										: '';
							echo "<option value=\"{$appointer->getId()}\"$selected>$name</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr><td><label for="maxCurrentTerms"><?php echo $this->_('labels.maxCurrentTerms'); ?></label></td>
				<td><input name="maxCurrentTerms" id="maxCurrentTerms" size="3" maxlength="3"
						value="<?php echo $this->seat->getMaxCurrentTerms(); ?>" />
				</td>
			</tr>

			<tr><td><label for="startDate"><?php echo $this->_('labels.date_start'); ?></label></td>
				<td><input name="startDate" id="startDate" value="<?php echo $this->seat->getStartDate(DATE_FORMAT); ?>" /></td>
			</tr>
			<tr><td><label for="endDate"><?php echo $this->_('labels.date_end'); ?></label></td>
				<td><input name="endDate" id="endDate" value="<?php echo $this->seat->getEndDate(DATE_FORMAT); ?>" /></td>
			</tr>

		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($this->seat->getUri());
		?>
	</fieldset>
</form>
