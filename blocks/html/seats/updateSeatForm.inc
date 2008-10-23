<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Seat $this->seat
 */
?>
<h1>Update Seat</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Seat Info</legend>
	<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
	<table>

	<tr><td><label for="seat-title" class="required">Title</label></td>
		<td><input name="seat[title]" id="seat-title" value="<?php echo View::escape($this->seat->getTitle()); ?>" /></td></tr>

	<tr><td><label for="seat-appointer_id">Appointed By</label></td>
		<td><select name="seat[appointer_id]" id="seat-appointer_id">
				<?php
					$list = new AppointerList();
					$list->find();
					foreach($list as $appointer)
					{
						$name = View::escape($appointer->getName());
						$selected = ($this->seat->getAppointer_id()==$appointer->getId()) ? ' selected="selected"' : '';
						echo "<option value=\"{$appointer->getId()}\"$selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="seat-maxCurrentMembers">Maximum Concurrent Members</label></td>
		<td><input name="seat[maxCurrentMembers]" id="seat-maxCurrentMembers" size="3" maxlength="3" value="<?php echo View::escape($this->seat->getMaxCurrentMembers()); ?>" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->seat->getCommittee()->getURL(); ?>';">Cancel</button>
</fieldset>
</form>
