<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Race $this->race
 */
?>
<h1>Update Race</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Race Info</legend>
	<input name="race_id" type="hidden" value="<?php echo $this->race->getId(); ?>" />
	<table>

	<tr><td><label for="race-name" class="required">Name</label></td>
		<td><input name="race[name]" id="race-name" value="<?php echo View::escape($this->race->getName()); ?>" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/races">Cancel</a>
</fieldset>
</form>