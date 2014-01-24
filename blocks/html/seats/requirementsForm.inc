<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\RequirementTable;
use Blossom\Classes\View;
?>
<form method="post" action="<?php echo BASE_URI; ?>/seats/updateRequirements">
	<?php
		if ($this->seat->hasRequirements()) {
			echo "
			<fieldset><legend>{$this->_('labels.current_requirements')}</legend>
			<table>
			";
				foreach ($this->seat->getRequirements() as $requirement) {
					$url = BASE_URI."/seats/removeRequirement?seat_id={$this->seat->getId()};requirement_id={$requirement->getId()}";
					$text = View::escape($requirement);
					echo "
					<tr><td><a class=\"fa fa-times\" href=\"$url\">
								<i class=\"hidden-label\">{$this->_('labels.delete')}</i>
							</a>
						</td>
						<td>$text</td>
					</tr>
					";
				}
			echo "
			</table>
			</fieldset>
			";
		}
	?>
	<fieldset><legend><?php echo $this->_('messages.add_existing_requirement'); ?></legend>
		<div><label for="requirement_id">
				<?php echo $this->_(['labels.requirement', 'labels.requirements', 2]); ?>
			</label>
		</div>
		<div><select name="requirement_id" id="requirement_id">
				<option></option>
				<?php
					$table = new RequirementTable();
					$requirements = $table->find();
					foreach ($requirements as $requirement) {
						$name = View::escape($requirement);
						echo "<option value=\"{$requirement->getId()}\">$name</option>";
					}
				?>
			</select>
		</div>
	</fieldset>

	<fieldset><legend><?php echo $this->_('messages.add_new_requirement'); ?></legend>
		<div><label for="text"><?php echo $this->_('labels.add_requirement'); ?></label></div>
		<div><textarea name="text" id="text" rows="3" cols="60"></textarea></div>
	</fieldset>

	<fieldset>
		<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
		<button type="submit"><i class="fa fa-save"></i> <?php echo $this->_('labels.submit'); ?></button>
		<a class="btn" href="<?php echo $this->seat->getUri(); ?>">
			<i class="fa fa-times"></i>
			<?php echo $this->_('labels.done'); ?>
		</a>
	</fieldset>
</form>
