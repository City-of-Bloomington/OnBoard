<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Office $this->office
 */
use Blossom\Classes\View;

$title = $this->office->getId() ? $this->_('edit_office') : $this->_('add_office');
$picker = $this->template->getHelper('datePicker');
?>
<h2><?php echo $title; ?></h2>
<h3><?php echo View::escape($this->office->getPerson()->getFullname()); ?></h3>
<form method="post" action="<?php echo BASE_URI; ?>/offices/update">
	<fieldset><legend><?php echo $this->_('info_office'); ?></legend>
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
			<tr><td><label for="title"><?php echo $this->_('title'); ?></label></td>
				<td><input name="title" id="title"
							value="<?php echo View::escape($this->office->getTitle()); ?>" />
				</td>
			</tr>

			<tr><td><label for="startDate"><?php echo $this->_('date_start'); ?></label></td>
				<td><?= $picker->datePicker('startDate', 'startDate', $this->office->getStartDate('U')); ?></td>
			</tr>
			<tr><td><label for="endDate"><?php echo $this->_('date_end'); ?></label></td>
				<td><?= $picker->datePicker('endDate', 'endDate', $this->office->getEndDate('U')); ?></td>
			</tr>
		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($this->office->getCommittee()->getUri());
		?>
	</fieldset>
</form>
