<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
?>
<h1>Add Seat</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Seat Info</legend>
	<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />
	<table>

	<tr><td><label for="seat-appointer_id">Appointed By</label></td>
		<td><select name="seat[appointer_id]" id="seat-appointer_id">
				<option value="">Elected</option>
				<?php
					$list = new AppointerList();
					$list->find();
					foreach($list as $appointer)
					{
						$name = View::escape($appointer->getName());
						echo "<option value=\"{$appointer->getId()}\">$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="seat-title">Title</label></td>
		<td><input name="seat[title]" id="seat-title" /></td></tr>

	<tr><td><label for="seat-requirements">Requirements</label></td>
		<td><input name="seat[requirements]" id="seat-requirements" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<?php
		$url = new URL(BASE_URL.'/committees/viewCommittee.php');
		$url->committee_id = $this->committee->getId();
	?>
	<a class="cancel button" href="<?php echo $url; ?>">Cancel</a>
</fieldset>
</form>