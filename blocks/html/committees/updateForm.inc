<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Application\Models\Committee;
use Application\Models\Person;
use Blossom\Classes\View;

$fields = [
    'type',
	'name', 'statutoryName', 'statuteReference', 'statuteUrl', 'website',
	'email', 'phone', 'address', 'city', 'state', 'zip',
	'description', 'contactInfo', 'meetingSchedule'
];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f = View::escape($this->committee->$get());
}

$title = $this->committee->getId() ? $this->_('edit_committee') : $this->_('add_committee');
?>
<h2><?php echo $title; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/committees/update">
	<fieldset><legend><?php echo $this->_('committee_info'); ?></legend>
		<input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />

		<table>
            <tr><td><label   for="type"><?= $this->_('type'); ?></label></td>
                <td>
                <?php
                    if (Person::isAllowed('committees', 'changeType')) {
                        echo '<select name="type" class="required">';
                        foreach (Committee::$types as $t) {
                            $selected = $type === $t
                                ? 'selected="true"'
                                : '';
                            echo "<option value=\"$t\" $selected>$t</option>";
                        }
                        echo '</select>';
                    }
                    else {
                        echo $type;
                    }
                ?>
                </td>
            </tr>
			<tr><td><label for="name" class="required"><?php echo $this->_('name'); ?></label></td>
				<td><input name="name" id="name" size="40"
						value="<?php echo $name; ?>" />
				</td>
			</tr>

			<tr><td><label for="yearFormed"><?php echo $this->_('year_formed'); ?></label></td>
				<td><input name="yearFormed" id="yearFormed" size="4" maxlength="4"
						value="<?php echo $this->committee->getYearFormed(); ?>" />
				</td>
			</tr>

			<tr><td><label for="statutoryName"><?php echo $this->_('statutoryName'); ?></label></td>
				<td><input name="statutoryName" id="statutoryName" size="40"
						value="<?php echo $statutoryName; ?>" />
				</td>
			</tr>

			<tr><td><label for="statuteReference"><?php echo $this->_('statuteReference'); ?></label></td>
				<td><input name="statuteReference" id="statuteReference" size="40"
						value="<?php echo $statuteReference; ?>" />
				</td>
			</tr>

			<tr><td><label for="statuteUrl"><?php echo $this->_('statuteUrl'); ?></label></td>
				<td><input name="statuteUrl" id="statuteUrl" size="40"
						value="<?php echo $statuteUrl; ?>" />
				</td>
			</tr>

			<tr><td><label for="website"><?php echo $this->_('website'); ?></label></td>
				<td><input name="website" id="website" size="40"
						value="<?php echo $website; ?>" />
				</td>
			</tr>
        </table>
    </fieldset>
    <fieldset><legend><?= $this->_('contactInfo'); ?></legend>
        <table>
            <tr><td><label  for="address"><?php echo $this->_('address'); ?></label></td>
                <td><textarea name="address" id="address" rows="3" cols="40"><?= $address; ?></textarea></td>
            </tr>
            <tr><td><label  for="city"><?php echo $this->_('city'); ?></label></td>
                <td><input name="city" id="city" size="40"
                        value="<?php echo $city; ?>" />
                </td>
            </tr>
            <tr><td><label  for="state"><?php echo $this->_('state'); ?></label></td>
                <td><input name="state" id="state" size="2"
                        value="<?php echo $state; ?>" />
                    <label><?= $this->_('zipcode'); ?>
                        <input name="zip" size="7" value="<?= $zip; ?>" />
                    </label>
                </td>
            </tr>
            <tr><td><label  for="email"><?php echo $this->_(['email', 'emails', 1]); ?></label></td>
                <td><input name="email" id="email" size="40"
                        value="<?php echo $email; ?>" />
                </td>
            </tr>
            <tr><td><label  for="phone"><?php echo $this->_('phone'); ?></label></td>
                <td><input name="phone" id="phone" size="40"
                        value="<?php echo $phone; ?>" />
                </td>
            </tr>
		</table>
    </fieldset>

    <fieldset><legend><?php echo $this->_('description'); ?></legend>
        <textarea name="description" id="description" rows="20" cols="80" class="markdown"><?php echo $description; ?></textarea>
    </fieldset>

    <fieldset><legend><?php echo $this->_('meetingSchedule'); ?></legend>
        <textarea name="meetingSchedule" id="meetingSchedule" rows="6" cols="80"><?php echo $meetingSchedule; ?></textarea>
    </fieldset>

    <fieldset><legend><?php echo $this->_('contactInfo'); ?></legend>
        <textarea name="contactInfo" id="contactInfo" rows="6" cols="80"><?php echo $contactInfo; ?></textarea>
    </fieldset>

	<fieldset><legend><?php echo $this->_('submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');

			$uri = $this->committee->getId()
                ? BASE_URI."/committees/view?committee_id={$this->committee->getId()}"
                : BASE_URI."/committees";
			echo $helper->saveAndCancelButtons($uri);
		?>
	</fieldset>
</form>
