<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
?>
<h1>Update Seat</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Seat Info</legend>
		<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
		<table>

			<tr><td><label for="seat-name" class="required">Name</label></td>
				<td><input name="seat[name]" id="seat-name" value="<?php echo View::escape($this->seat->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="seat-appointer_id" class="required">Appointer</label></td>
				<td><select name="seat[appointer_id]" id="seat-appointer_id">
						<?php
							$list = new AppointerList();
							$list->find();
							foreach ($list as $appointer) {
								$name = View::escape($appointer->getName());
								$selected = ($this->seat->getAppointer_id()==$appointer->getId())
											? ' selected="selected"'
											: '';
								echo "
								<option value=\"{$appointer->getId()}\"$selected>
									$name
								</option>
								";
							}
						?>
					</select>
				</td>
			</tr>

			<tr><td><label for="seat-maxCurrentTerms">Maximum Concurrent Terms</label></td>
				<td><input name="seat[maxCurrentTerms]" id="seat-maxCurrentTerms" size="3" maxlength="3" value="<?php echo $this->seat->getMaxCurrentTerms(); ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->seat->getURL(); ?>';">
			Cancel
		</button>
	</fieldset>
</form>
