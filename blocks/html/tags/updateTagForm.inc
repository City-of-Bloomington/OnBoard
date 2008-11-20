<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Tag $this->tag
 */
?>
<h1>Update Tag</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Tag Info</legend>
	<input name="tag_id" type="hidden" value="<?php echo $this->tag->getId(); ?>" />
	<table>

	<tr><td><label for="tag-name" class="required">Name</label></td>
		<td><input name="tag[name]" id="tag-name" value="<?php echo View::escape($this->tag->getName()); ?>" /></td></tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="button" onclick="document.location.href='<?php echo BASE_URL; ?>/tags';">Cancel</button>
</fieldset>
</form>
