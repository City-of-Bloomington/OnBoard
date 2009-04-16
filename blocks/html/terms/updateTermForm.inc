<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 */
?>
<h1>Update Term</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
	<fieldset><legend>Term Info</legend>
		<input name="term_id" type="hidden" value="<?php echo $this->term->getId(); ?>" />

		<h2><?php echo View::escape($this->term->getPerson()->getFullname()); ?></h2>
		<table>
			<tr><td><label for="term-term_start" class="required">Term Start</label></td>
				<td><input name="term[term_start]" id="term-term_start" value="<?php echo $this->term->getTerm_start('n/j/Y'); ?>" onfocus="FRAMEWORK.popupCalendar(this);" />
				</td>
			</tr>
			<tr><td><label for="term-term_end">Term End</label></td>
				<td><input name="term[term_end]" id="term-term_end" value="<?php echo $this->term->getTerm_end('n/j/Y'); ?>" onfocus="FRAMEWORK.popupCalendar(this);" />
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->term->getSeat()->getCommittee()->getURL(); ?>';">
			Cancel
		</button>
	</fieldset>
</form>
