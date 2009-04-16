<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat_id
 */
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<?php
	if ($this->seat->hasRequirements()) {
		echo "
		<fieldset><legend>Current Requirements</legend>
		<table>
		";
			$url = new URL(BASE_URL.'/seats/removeRequirement.php');
			$url->seat_id = $this->seat->getId();
			foreach ($this->seat->getRequirements() as $requirement) {
				$url->requirement_id = $requirement->getId();
				$text = View::escape($requirement);
				echo "
				<tr><td><a class=\"delete button\" href=\"$url\">Delete</a></td>
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
	<fieldset><legend>Add an existing Requirement</legend>
		<div><label for="requirement_id">Existing Requirements</label>
		<div><select name="requirement_id" id="requirement_id">
				<option></option>
				<?php
					$requirements = new RequirementList();
					$requirements->find();
					foreach ($requirements as $requirement) {
						$name = View::escape($requirement);
						echo "<option value=\"{$requirement->getId()}\">$name</option>";
					}
				?>
			</select>
		</div>
	</fieldset>

	<fieldset><legend>Add a new Requirement to the system</legend>
		<div><label for="text">New Requirement</label></div>
		<div><textarea name="text" id="text" rows="3" cols="60"></textarea></div>
	</fieldset>

	<fieldset>
		<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
		<button type="submit" class="submit">Submit</button>
		<button type="button" cancel="done" onclick="document.location.href='<?php echo $this->seat->getURL(); ?>';">
			Done
		</button>
	</fieldset>
</form>
