<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Member $this->member
 */
?>
<h1>Edit Member</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Term</legend>
	<input name="member_id" type="hidden" value="<?php echo $this->member->getId(); ?>" />

	<table>
	<tr><th>User</th>
		<th><label for="member-term_start">Term Start</label></th>
		<th><label for="member-term_end">Term End</label></th>
	</tr>

	<tr><th><?php
				echo View::escape($this->member->getUser()->getFullname());
			?>
		</th>
		<td><input name="member[term_start]"
					id="member-term_start"
					value="<?php echo $this->member->getTerm_start('n/j/Y'); ?>"
					onfocus="FRAMEWORK.popupCalendar(this);" />
		</td>
		<td><input name="member[term_end]"
					id="member-term_end"
					value="<?php echo $this->member->getTerm_end('n/j/Y'); ?>"
					onfocus="FRAMEWORK.popupCalendar(this);" />
		</td>
	</tr>
	</table>
</fieldset>

<?php
	$this->user = $this->member->getUser();
	include APPLICATION_HOME.'/blocks/html/users/personalInfoFields.inc';
?>

<fieldset><legend>Submit</legend>
	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->member->getSeat()->getURL(); ?>';">Cancel</button>
</fieldset>
</form>
