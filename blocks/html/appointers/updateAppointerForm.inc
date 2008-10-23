<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Appointer $this->appointer
 */
?>
<h1>Update Appointer</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Appointer Info</legend>
	<input name="appointer_id" type="hidden" value="<?php echo $this->appointer->getId(); ?>" />
	<table>

	<tr><td><label for="appointer-name" class="required">Name</label></td>
		<td><input name="appointer[name]"
					id="appointer-name"
					value="<?php echo View::escape($this->appointer->getName()); ?>" />
		</td>
	</tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/appointers';">Cancel</button>
</fieldset>
</form>
