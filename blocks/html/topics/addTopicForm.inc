<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Add Legislation</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Legislation Info</legend>
	<table>
	<tr><td><label>Committee</label></td><td><?php echo $this->committee->getName(); ?></td></tr>
	<tr><td><label for="topic-type">Type</label></td>
		<td><select name="topic[type]" id="topic-type"><option></option>
			<option>Ordinance</option>
			<option>Resolution</option>
			<option>App. Ordinance</option>
		</select></td></tr>
	<tr><td><label for="topic-date">Date</label></td>
		<td><input name="topic[date]" id="topic-date" onfocus="FRAMEWORK.popupCalendar(this);" maxlength="10" />
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