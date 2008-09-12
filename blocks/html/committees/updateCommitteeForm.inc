<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<h1>Update Committee</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Committee Info</legend>
	<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />
	<table>

	<tr><td><label for="committee-name" class="required">Name</label></td>
		<td><input name="committee[name]" id="committee-name" value="<?php echo $this->committee->getName(); ?>" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/committees">Cancel</a>
</fieldset>
</form>