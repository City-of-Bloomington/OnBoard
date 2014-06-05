<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Office $this->office
 */
use Blossom\Classes\View;

$title = $this->office->getId() ? $this->_('labels.edit_office') : $this->_('labels.add_office');
?>
<h2><?php echo $title; ?></h2>
<h3><?php echo View::escape($this->office->getPerson()->getFullname()); ?></h3>
<form method="post" action="<?php echo BASE_URI; ?>/offices/update">
	<fieldset><legend><?php echo $this->_('labels.info_office'); ?></legend>
		<?php
			if ($this->office->getId()) {
				echo "
				<input name=\"office_id\" type=\"hidden\" value=\"{$this->office->getId()}\" />
				";
			}
			else {
				echo "
				<input name=\"committee_id\" type=\"hidden\" value=\"{$this->office->getCommittee_id()}\" />
				<input name=\"person_id\" type=\"hidden\" value=\"{$this->office->getPerson_id()}\" />
				";
			}
		?>

		<table>
			<tr><td><label for="title"><?php echo $this->_('labels.title'); ?></label></td>
				<td><input name="title" id="title"
							value="<?php echo View::escape($this->office->getTitle()); ?>" />
				</td>
			</tr>

			<tr><td><label for="startDate"><?php echo $this->_('labels.date_start'); ?></label></td>
				<td><input name="startDate" id="startDate"
							value="<?php echo $this->office->getStartDate(DATE_FORMAT); ?>" />
				</td>
			</tr>
			<tr><td><label for="endDate"><?php echo $this->_('labels.date_end'); ?></label></td>
				<td><input name="endDate" id="endDate"
							value="<?php echo $this->office->getEndDate(DATE_FORMAT); ?>" />
				</td>
			</tr>
		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($this->office->getCommittee()->getUri());
		?>
	</fieldset>
</form>
