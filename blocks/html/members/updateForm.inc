<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 */

$title = $this->term->getId() ? $this->_('edit_term') : $this->_('add_term');
?>
<div>
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= BASE_URI; ?>/terms/update">
		<fieldset><legend><?= $this->_('info_term'); ?></legend>
			<input name="term_id"      type="hidden" value="<?= $this->term->getId();           ?>" />
			<input name="seat_id"      type="hidden" value="<?= $this->term->getSeat_id();      ?>" />

			<table>
				<tr><td><label for="person_id" class="required">
                            <?= $this->_(['person', 'people', 1]); ?>
                        </label>
                    </td>
					<td><?php
							$person = $this->term->getPerson();
							$helper = $this->template->getHelper('personChooser');
							echo $helper->personChooser('person', $person);
						?>
					</td>
				</tr>
				<tr><td><label  for="startDate" class="required"><?= $this->_('startDate'); ?></label></td>
					<td><input name="startDate" id="startDate"
							value="<?= $this->term->getStartDate(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr><td><label  for="endDate"><?= $this->_('endDate'); ?></label></td>
					<td><input name="endDate" id="endDate"
							value="<?= $this->term->getEndDate(DATE_FORMAT); ?>" />
					</td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->return_url);
			?>
		</fieldset>
	</form>
</div>
