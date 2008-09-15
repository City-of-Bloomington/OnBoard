<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Update Vote</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Vote Info</legend>
	<input name="id" type="hidden" value="<?php echo $this->vote->getId(); ?>" />
	<table>

	<tr><td><label for="vote-date-mon">date</label></td>
		<td><select name="vote[date][mon]" id="vote-date-mon"><option></option>
			<?php
				$date = $this->vote->dateStringToArray($this->vote->getDate());
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$date['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="vote[date][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$date['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="vote[date][year]" id="vote-date-year" size="4" maxlength="4" value="<?php echo $date['year']; ?>" />
		</td>
	</tr>
	<tr><td><label for="vote-type">type</label></td>
		<td><input name="vote[type]" id="vote-type" value="<?php echo $this->vote->getType(); ?>" /></td></tr>
		
	<tr><td><label for="vote-topic_id">topic_id</label></td>
		<td><input name="vote[topic_id]" id="vote-topic_id" value="<?php echo $this->vote->getTopic_id(); ?>" /></td></tr>
		
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votes">Cancel</a>
</fieldset>
</form>