<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 */
use Blossom\Classes\View;

$title = $this->term->getId() ? $this->_('labels.edit_term') : $this->_('labels.add_term');
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URL; ?>/terms/update">
		<fieldset><legend><?php echo $this->_('labels.info_term'); ?></legend>
			<input name="return_url" type="hidden" value="<?php echo $this->return_url; ?>" />
			<?php
				if ($this->term->getId()) {
					echo "<input name=\"term_id\" type=\"hidden\" value=\"{$this->term->getId()}\" />";
				}
				else {
					echo "<input name=\"seat_id\" type=\"hidden\" value=\"{$this->term->getSeat_id()}\" />";
				}
			?>

			<table>
				<tr><td><label for="person_id" class="required"><?php echo $this->_(['labels.person', 'labels.people', 1]); ?></label></td>
					<td><?php
							$person = $this->term->getPerson();
							$helper = $this->template->getHelper('personChooser');
							echo $helper->personChooser('person', $person);
						?>
					</td>
				</tr>
				<tr><td><label for="term_start" class="required">
							<?php echo $this->_('labels.date_start'); ?>
						</label>
					</td>
					<td><input name="term_start" id="term_start"
							value="<?php echo $this->term->getTerm_start(DATE_FORMAT); ?>" />
					</td>
				</tr>
				<tr><td><label for="term_end"><?php echo $this->_('labels.date_end'); ?></label></td>
					<td><input name="term_end" id="term_end"
							value="<?php echo $this->term->getTerm_end(DATE_FORMAT); ?>" />
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
