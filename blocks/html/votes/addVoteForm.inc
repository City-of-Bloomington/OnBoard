<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$types = new VoteTypeList()
$types->find();

?>
<h1>Add Vote</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Vote Info</legend>
	<table>

	<tr><td><label for="vote-date">Date</label></td>

		<td><input name="vote[date]" id="vote-date" size="10" maxlength="10" value="" onfocus="FRAMEWORK.popupCalendar(this);" />

		</td>
	</tr>
	<tr><td><label for="vote-voteType_id">Type</label></td>
		<td><select name="vote[voteType_id]" id="vote-voteType_id">
	<?php 
		foreach($types as $type){
			echo "<option value=\"{$type->getId()}\">$type</option>";
		}
	?>
	</select>
	<tr><td><label for="vote-topic_id">topic_id</label></td>
		<td><input name="vote[topic_id]" id="vote-topic_id" /></td></tr>
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votes">Cancel</a>
</fieldset>
</form>