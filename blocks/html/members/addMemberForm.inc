<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Seat $this->seat
 * @param User $this->user
 */
?>
<h1>Add Member</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Term</legend>
	<input name="seat_id" type="hidden" value="<?php echo $this->seat->getId(); ?>" />
	<input name="user_id" type="hidden" value="<?php echo $this->user->getId(); ?>" />

	<table>
	<tr><th>User</th>
		<th><label for="member-term_start" class="required">Term Start</label></th>
		<th><label for="member-term_end">Term End</label></th>
	</tr>

	<tr><th><?php
				echo View::escape($this->user->getFirstname());
				echo View::escape($this->user->getLastname());
			?>
		</th>
		<td><input name="member[term_start]"
					id="member-term_start"
					value="<?php echo date('n/j/Y'); ?>"
					onfocus="FRAMEWORK.popupCalendar(this);" />
		</td>
		<td><input name="member[term_end]" id="member-term_end" onfocus="FRAMEWORK.popupCalendar(this);" /></td>
	</tr>
	</table>
</fieldset>

<?php
	include APPLICATION_HOME.'/blocks/html/users/personalInfoFields.inc';
?>

<fieldset><legend>Submit</legend>
	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->seat->getURL(); ?>';">Cancel</button>
</fieldset>
</form>
