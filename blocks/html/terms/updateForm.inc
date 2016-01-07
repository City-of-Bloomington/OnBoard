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
			<input name="return_url"   type="hidden" value="<?= $this->return_url;              ?>" />
			<input name="term_id"      type="hidden" value="<?= $this->term->getId();           ?>" />
			<input name="committee_id" type="hidden" value="<?= $this->term->getCommittee_id(); ?>" />
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
				<tr><td><label for="term_start" class="required">
							<?= $this->_('date_start'); ?>
						</label>
					</td>
					<td><input name="term_start" id="term_start"
							value="<?= $this->term->getTerm_start(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr><td><label for="term_end"><?= $this->_('date_end'); ?></label></td>
					<td><input name="term_end" id="term_end"
							value="<?= $this->term->getTerm_end(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr><td><label for="carryover"><?= $this->_('carryover'); ?></label></td>
                    <td><input name="carryover" id="carryover" type="checkbox" value="1"
                            <?= $this->term->isCarryingOver() ? 'checked="true"' : ''; ?> />
                    </td>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->return_url);
			?>
		</fieldset>
	</form>
</div>
