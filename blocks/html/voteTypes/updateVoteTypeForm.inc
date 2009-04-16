<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param VoteType $this->voteType
 */
?>
<h1>Update VoteType</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>VoteType Info</legend>
		<input name="voteType_id" type="hidden" value="<?php echo $this->voteType->getId(); ?>" />
		<table>

			<tr><td><label for="voteType-name" class="required">Name</label></td>
				<td><input name="voteType[name]" id="voteType-name" value="<?php echo View::escape($this->voteType->getName()); ?>" />
				</td>
			</tr>
			<tr><td><label for="voteType-ordering" class="required">Order</label></td>
				<td><input name="voteType[ordering]" id="voteType-ordering" value="<?php echo $this->voteType->getOrdering(); ?>" />
				</td>
			</tr>

		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/voteTypes';">
			Cancel
		</button>
	</fieldset>
</form>
