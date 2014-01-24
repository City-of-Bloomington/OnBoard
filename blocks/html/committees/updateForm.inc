<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\View;

$fields = ['name','statutoryName','statuteReference','website','description'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = View::escape($this->committee->$get());
}

$title = $this->committee->getId() ? $this->_('labels.edit_committee') : $this->_('labels.add_committee');
?>
<h2><?php echo $title; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/committees/update">
	<fieldset><legend><?php echo $this->_('labels.info_committee'); ?></legend>
		<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />

		<table>
			<tr><td><label for="name" class="required"><?php echo $this->_('labels.name'); ?></label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>

			<tr><td><label for="dateFormed"><?php echo $this->_('labels.date_formed'); ?></label></td>
				<td><input name="dateFormed" id="dateFormed" size="10" maxlength="10"
						value="<?php echo $this->committee->getDateFormed(DATE_FORMAT); ?>" />
				</td>
			</tr>

			<tr><td><label for="statutoryName"><?php echo $this->_('labels.statutoryName'); ?></label></td>
				<td><input name="statutoryName" id="statutoryName" size="40"
						value="<?php echo $statutoryName; ?>" />
				</td>
			</tr>

			<tr><td><label for="statuteReference"><?php echo $this->_('labels.statuteReference'); ?></label></td>
				<td><input name="statuteReference" id="statuteReference" size="40"
						value="<?php echo $statuteReference; ?>" />
				</td>
			</tr>

			<tr><td><label for="website"><?php echo $this->_('labels.website'); ?></label></td>
				<td><input name="website" id="website" size="40"
						value="<?php echo $website; ?>" />
				</td>
			</tr>

			<tr><td colspan="2">
					<div><label for="description"><?php echo $this->_('labels.description'); ?></label></div>
					<textarea name="description" id="description" rows="3" cols="60"><?php echo $description; ?></textarea>
				</td>
			</tr>
		</table>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/committees');
		?>
	</fieldset>
</form>
