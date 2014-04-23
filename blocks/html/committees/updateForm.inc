<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Blossom\Classes\View;

$fields = [
	'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'website',
	'description', 'synopsis', 'contactInfo', 'meetingSchedule'
];
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
				<td><input name="name" id="name" size="40"
						value="<?php echo $name; ?>" />
				</td>
			</tr>

			<tr><td><label for="yearFormed"><?php echo $this->_('labels.year_formed'); ?></label></td>
				<td><input name="yearFormed" id="yearFormed" size="4" maxlength="4"
						value="<?php echo $this->committee->getYearFormed(); ?>" />
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

			<tr><td><label for="statuteUrl"><?php echo $this->_('labels.statuteUrl'); ?></label></td>
				<td><input name="statuteUrl" id="statuteUrl" size="40"
						value="<?php echo $statuteUrl; ?>" />
				</td>
			</tr>

			<tr><td><label for="website"><?php echo $this->_('labels.website'); ?></label></td>
				<td><input name="website" id="website" size="40"
						value="<?php echo $website; ?>" />
				</td>
			</tr>

		</table>

		<div>
			<div><label for="synopsis"><?php echo $this->_('labels.synopsis'); ?></label></div>
			<textarea name="synopsis" id="synopsis" rows="3" cols="60"><?php echo $synopsis; ?></textarea>
		</div>
		<div>
			<div><label for="contactInfo"><?php echo $this->_('labels.contactInfo'); ?></label></div>
			<textarea name="contactInfo" id="contactInfo" rows="3" cols="60"><?php echo $contactInfo; ?></textarea>
		</div>
		<div>
			<div><label for="meetingSchedule"><?php echo $this->_('labels.meetingSchedule'); ?></label></div>
			<textarea name="meetingSchedule" id="meetingSchedule" rows="3" cols="60"><?php echo $meetingSchedule; ?></textarea>
		</div>
	</fieldset>

	<fieldset><legend><?php echo $this->_('labels.description'); ?></legend>
		<textarea name="description" id="description" rows="20" cols="80" class="markdown"><?php echo $description; ?></textarea>
	</fieldset>

	<fieldset><legend><?php echo $this->_('labels.submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/committees');
		?>
	</fieldset>
</form>
