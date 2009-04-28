<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Officer $this->officer
 */
?>
<h1>Update Officer</h1>
<h2><?php echo View::escape($this->officer->getPerson()->getFullname()); ?></h2>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
	<fieldset><legend>Office Info</legend>
		<input name="officer_id" type="hidden" value="<?php echo $this->officer->getId(); ?>" />
		<table>
			<tr><td><label for="officer-title">Title</label></td>
				<td><input name="officer[title]" id="officer-title" value="<?php echo $this->officer->getTitle(); ?>" />
				</td>
			</tr>

			<tr><td><label for="officer-startDate">Start Date</label></td>
				<td><input name="officer[startDate]"
							id="officer-startDate"
							value="<?php echo $this->officer->getStartDate('n/j/Y'); ?>"" />
				</td>
			</tr>
			<tr><td><label for="officer-endDate">End Date</label></td>
				<td><input name="officer[endDate]"
							id="officer-endDate"
							value="<?php echo $this->officer->getEndDate('n/j/Y'); ?>" />
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->officer->getCommittee()->getURL(); ?>';">
			Cancel
		</button>
	</fieldset>
</form>
