<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
?>
<h1>Add Legislation</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Legislation Info</legend>
	<input type="hidden" name="committee_id" id="committee_id" value="<?php echo $this->committee->getId(); ?>" />

	<table>
	<tr><td><label>Committee</label></td><td><?php echo $this->committee->getName(); ?></td></tr>
	<tr><td><label for="topic-type">Type</label></td>
		<td><select name="topic[topicType_id]" id="topic-type"><option></option>
			<?php
				$options = new TopicTypeList();
				$options->find();
				foreach($options as $type) {
					echo "<option value=\"{$type->getId()}\">$type</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr><td><label for="topic-date">Date</label></td>
		<td><input name="topic[date]" id="topic-date" onfocus="FRAMEWORK.popupCalendar(this);" size="10" />
		</td>
	</tr>
	<tr><td><label for="topic-number">Number</label></td>
		<td><input name="topic[number]" id="topic-number" /></td></tr>
	<tr><td><label for="topic-description">Description</label></td>
		<td><textarea name="topic[description]" id="topic-description" rows="3" cols="60"></textarea></td></tr>
	<tr><td><label for="topic-synopsis">Dynopsis</label></td>
		<td><textarea name="topic[synopsis]" id="topic-synopsis" rows="3" cols="60"></textarea></td></tr>

	</table>
	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo $this->committee->getURL(); ?>">Cancel</a>
</fieldset>
</form>