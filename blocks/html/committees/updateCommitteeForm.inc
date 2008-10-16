<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
?>
<h1>Update Committee</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Committee Info</legend>
	<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />
	<table>

	<tr><td><label for="committee-name" class="required">Name</label></td>
		<td><input name="committee[name]" id="committee-name" value="<?php echo $this->committee->getName(); ?>" /></td></tr>

	<tr><td><label for="committee-dateFormed-mon" class="required">Date Formed</label></td>
		<td><select name="committee[dateFormed][mon]" id="committee-dateFormed-mon"><option></option>
			<?php
				$dateFormed = getdate($this->committee->getDateFormed());
				for($i=1; $i<=12; $i++)
				{
					if ($i!=$dateFormed['mon']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<select name="committee[dateFormed][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					if ($i!=$dateFormed['mday']) { echo "<option>$i</option>"; }
					else { echo "<option selected=\"selected\">$i</option>"; }
				}
			?>
			</select>
			<input name="committee[dateFormed][year]" id="committee-dateFormed-year" size="4" maxlength="4" value="<?php echo $dateFormed['year']; ?>" />
		</td>
	</tr>

	<tr><td><label for="committee-statutoryName">Statutory Name</label></td>
		<td><input name="committee[statutoryName]" id="committee-statutoryName" value="<?php echo $this->committee->getStatutoryName(); ?>" /></td></tr>

	<tr><td><label for="committee-statuteReference">Statute Reference</label></td>
		<td><input name="committee[statuteReference]" id="committee-statuteReference" value="<?php echo $this->committee->getStatuteReference(); ?>" /></td></tr>

	<tr><td><label for="committee-website">Website</label></td>
		<td><input name="committee[website]" id="committee-website" value="<?php echo $this->committee->getWebsite(); ?>" /></td></tr>

	<tr><td><label for="committee-description">Description</label></td>
		<td><textarea name="committee[description]" id="committee-description" rows="3" cols="60"><?php echo $this->committee->getDescription(); ?></textarea></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/committees">Cancel</a>
</fieldset>
</form>
