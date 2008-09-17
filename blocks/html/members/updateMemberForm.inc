<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Update Member</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Member Info</legend>
	<input name="id" type="hidden" value="<?php echo $this->member->getId(); ?>" />
	<table>

	<tr><td><label for="member-seat_id">seat_id</label></td>
		<td><input name="member[seat_id]" id="member-seat_id" value="<?php echo $this->member->getSeat_id(); ?>" /></td></tr>
		
	<tr><td><label for="member-user_id">user_id</label></td>
		<td><input name="member[user_id]" id="member-user_id" value="<?php echo $this->member->getUser_id(); ?>" /></td></tr>
		
	<tr><td><label for="member-term_start-mon">term_start</label></td>
		<td><select name="member[term_start][mon]" id="member-term_start-mon"><option></option>
			<?php
				$term_start = $this->member->dateStringToArray($this->member->getTerm_start());
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$term_start['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="member[term_start][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$term_start['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="member[term_start][year]" id="member-term_start-year" size="4" maxlength="4" value="<?php echo $term_start['year']; ?>" />
		</td>
	</tr>
	<tr><td><label for="member-term_end-mon">term_end</label></td>
		<td><select name="member[term_end][mon]" id="member-term_end-mon"><option></option>
			<?php
				$term_end = $this->member->dateStringToArray($this->member->getTerm_end());
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$term_end['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="member[term_end][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$term_end['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="member[term_end][year]" id="member-term_end-year" size="4" maxlength="4" value="<?php echo $term_end['year']; ?>" />
		</td>
	</tr>
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/members">Cancel</a>
</fieldset>
</form>