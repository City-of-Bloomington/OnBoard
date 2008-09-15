<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Add Topic</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Topic Info</legend>
	<table>
	<tr><td><label>Committee</label></td><td><?php echo $this->committee->getName(); ?></td></tr>
	
	<tr><td><label for="topic-date-mon">Date</label></td>
		<td><select name="topic[date][mon]" id="topic-date-mon"><option></option>
			<?php
				$now = getdate();
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$now['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="topic[date][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$now['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="topic[date][year]" id="topic-date-year" size="4" maxlength="4" value="<?php echo $now['year']; ?>" />
		</td>
	</tr>
	<tr><td><label for="topic-number">Number</label></td>
		<td><input name="topic[number]" id="topic-number" /></td></tr>
	<tr><td><label for="topic-description">Description</label></td>
		<td><textarea name="topic[description]" id="topic-description" rows="3" cols="60"></textarea></td></tr>
	<tr><td><label for="topic-synopsis">Dynopsis</label></td>
		<td><textarea name="topic[synopsis]" id="topic-synopsis" rows="3" cols="60"></textarea></td></tr>
				
	</table>
		<input type="hidden" name="topic[committee_id]" id="topic-committee_id" value="<?php echo $this->committee->getId(); ?>" />
		<input type="hidden" name="committee_id" id="committee_id" value="<?php echo $this->committee->getId(); ?>" />
	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/topics">Cancel</a>
</fieldset>
</form>