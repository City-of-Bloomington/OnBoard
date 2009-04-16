<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 * @param Person $this->person
 */
?>
<h1>Add Office</h1>
<h2><?php echo View::escape($this->person->getFullname()); ?></h2>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
	<fieldset><legend>Office Info</legend>
		<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />
		<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
		<table>
			<tr><td><label for="officer-title" class="required">Title</label></td>
				<td><input name="officer[title]" id="officer-title" />
				</td>
			</tr>

			<tr><td><label for="officer-startDate" class="required">Start Date</label></td>
				<td><input name="officer[startDate]"
							id="officer-startDate"
							value="<?php echo date('n/j/Y'); ?>"
							onfocus="FRAMEWORK.popupCalendar(this);" />
				</td>
			</tr>
			<tr><td><label for="officer-endDate">End Date</label></td>
				<td><input name="officer[endDate]"
							id="officer-endDate"
							onfocus="FRAMEWORK.popupCalendar(this);" />
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->committee->getURL(); ?>';">
			Cancel
		</button>
	</fieldset>
</form>
