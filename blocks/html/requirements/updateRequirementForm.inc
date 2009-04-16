<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Requirement $this->requirement
 */
?>
<h1>Update Requirement</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Requirement Info</legend>
	<input name="requirement_id" type="hidden" value="<?php echo $this->requirement->getId(); ?>" />

	<div><label for="requirement-text" class="required">Text</label></div>
	<div><textarea name="requirement[text]" id="requirement-text" rows="3" cols="60"><?php echo View::escape($this->requirement->getText()); ?></textarea>
	</div>

	<button type="submit" class="submit">Submit</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/requirements';">Cancel</button>
</fieldset>
</form>
