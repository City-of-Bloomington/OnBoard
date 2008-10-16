<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Add Committee</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Committee</legend>

	<table>

	<tr><td><label for="committee-name" class="required">Name</label></td>
		<td><input name="committee[name]" id="committee-name" /></td></tr>

	<tr><td><label for="committee-dateFormed-mon" class="required">Date Formed</label></td>
		<td><select name="committee[dateFormed][mon]" id="committee-dateFormed-mon"><option></option>
			<?php
				$now = getdate();
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$now['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="committee[dateFormed][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$now['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="committee[dateFormed][year]" id="committee-dateFormed-year" size="4" maxlength="4" value="<?php echo $now['year']; ?>" />
		</td>
	</tr>

	<tr><td><label for="committee-statutoryName">Statutory Name</label></td>
		<td><input name="committee[statutoryName]" id="committee-statutoryName" /></td></tr>

	<tr><td><label for="committee-statuteReference">Statute Reference</label></td>
		<td><input name="committee[statuteReference]" id="committee-statuteReference" /></td></tr>

	<tr><td><label for="committee-website">Website</label></td>
		<td><input name="committee[website]" id="committee-website" /></td></tr>

	<tr><td><label for="committee-description">Description</label></td>
		<td><textarea name="committee[description]" id="committee-description" rows="3" cols="60"></textarea></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/committees">Cancel</a>
</fieldset>
</form>
