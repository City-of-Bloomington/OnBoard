<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param TopicType $this->topicType
 */
?>
<h1>Update Legislation Type</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Legislation Type Info</legend>
	<input name="topicType_id" type="hidden" value="<?php echo $this->topicType->getId(); ?>" />
	<table>

	<tr><td><label for="topicType-name" class="required">Name</label></td>
		<td><input name="topicType[name]"
					id="topicType-name"
					value="<?php echo $this->topicType->getName(); ?>" />
		</td>
	</tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/topicTypes">Cancel</a>
</fieldset>
</form>