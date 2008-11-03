<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Topic $this->topic
 */
?>
<h1>Update Legislation</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Legislation Info</legend>
	<input name="topic_id" type="hidden" value="<?php echo $this->topic->getId(); ?>" />

	<table>
	<tr><td><label for="topic-type" class="required">Type</label></td>
		<td><select name="topic[topicType_id]" id="topic-type">
				<?php
					$options = new TopicTypeList();
					$options->find();
					foreach($options as $type) {
						$name = View::escape($type);
						$selected = $type->getId()==$this->topic->getTopicType_id() ? ' selected="selected"' : '';
						echo "<option value=\"{$type->getId()}\"$selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>

	<tr><td><label for="topic-date" class="required">Date</label></td>
		<td><input name="topic[date]"
					id="topic-date"
					value="<?php echo $this->topic->getDate('n/j/Y'); ?>"
					onfocus="FRAMEWORK.popupCalendar(this);" maxlength="10" size="10" />
		</td>
	</tr>

	<tr><td><label for="topic-number" class="required">Number</label></td>
		<td><input name="topic[number]"
					id="topic-number"
					value="<?php echo View::escape($this->topic->getNumber()); ?>" />
		</td>
	</tr>

	<tr><td><label for="topic-description" class="required">Description</label></td>
		<td><textarea name="topic[description]" id="topic-description" rows="3" cols="60"><?php echo View::escape($this->topic->getDescription()); ?></textarea></td></tr>

	<tr><td><label for="topic-synopsis" class="required">Synopsis</label></td>
		<td><textarea name="topic[synopsis]" id="topic-synopsis" rows="3" cols="60"><?php echo View::escape($this->topic->getSynopsis()); ?></textarea></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->topic->getCommittee()->getURL(); ?>';">Cancel</button>
</fieldset>
</form>
