<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$types = array("","Do-Pass","Final");
?>
<h1>Add Vote</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Vote Info</legend>
	<table>

	<tr><td><label for="vote-date">Date</label></td>
		<td><input name="vote[date]" id="vote-date" size="10" maxlength="10" onfocus="FRAMEWORK.popupCalendar(this);" />
		</td>
	</tr>
	<tr><td><label for="vote-voteType_id">Type</label></td>
		<td><select name="vote[voteType_id]" id="vote-voteType_id">
	<?php 
		for($i=0;$i<count($types);$i++){
		    echo "<option value=\"$i\">{$types[$i]}</option>";
		}
	?>
	</select></td></tr>
		
		<td><input type="hidden" name="vote[topic_id]" value="<?php echo $this->topic->getId(); ?>" /></td></tr>
		
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votes">Cancel</a>
</fieldset>
</form>