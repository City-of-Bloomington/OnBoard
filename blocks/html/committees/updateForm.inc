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

$title = $this->committee->getId() ? $this->_('committee_edit') : $this->_('committee_add');
?>
<section class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h1 class="fn1-uiBlock-title"><?= $this->_($title); ?></h1>
  </header>
  <div class="fn1-uiBlock-content">
    <form method="post" action="<?php echo BASE_URI; ?>/committees/update">
      <fieldset><legend><?php echo $this->_('committee_info'); ?></legend>
        <input name="committee_id" type="hidden" value="<?php echo $this->committee->getId(); ?>" />

          <dl class="fn1-input-field">
            <dt><label   for="type"><?= $this->_('type'); ?></label></dt>
            <dd>
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
            </dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label for="name" class="required"><?php echo $this->_('name'); ?></label></dt>
            <dd><input name="name" id="name" size="40"
              value="<?php echo $name; ?>" />
            </dd>
          </dl>

          <dl class="fn1-input-field">
            <dt><label for="yearFormed"><?php echo $this->_('year_formed'); ?></label></dt>
            <dd><input name="yearFormed" id="yearFormed" size="4" maxlength="4"
              value="<?php echo $this->committee->getYearFormed(); ?>" />
            </dd>
          </dl>

          <dl class="fn1-input-field">
            <dt><label for="statutoryName"><?php echo $this->_('statutoryName'); ?></label></dt>
            <dd><input name="statutoryName" id="statutoryName" size="40"
              value="<?php echo $statutoryName; ?>" />
            </dd>
          </dl>

          <dl class="fn1-input-field">
            <dt><label for="statuteReference"><?php echo $this->_('statuteReference'); ?></label></dt>
            <dd><input name="statuteReference" id="statuteReference" size="40"
              value="<?php echo $statuteReference; ?>" />
            </dd>
          </dl>

          <dl class="fn1-input-field">
            <dt><label for="statuteUrl"><?php echo $this->_('statuteUrl'); ?></label></dt>
            <dd><input name="statuteUrl" id="statuteUrl" size="40"
              value="<?php echo $statuteUrl; ?>" />
            </dd>
          </dl>

          <dl class="fn1-input-field">
            <dt><label for="website"><?php echo $this->_('website'); ?></label></dt>
            <dd><input name="website" id="website" size="40"
              value="<?php echo $website; ?>" />
            </dd>
          </dl>
        </fieldset>
        <fieldset><legend><?= $this->_('contactInfo'); ?></legend>
          <dl class="fn1-input-field">
            <dt><label  for="address"><?php echo $this->_('address'); ?></label></dt>
              <dd><textarea name="address" id="address" rows="3" cols="40"><?= $address; ?></textarea></dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label  for="city"><?php echo $this->_('city'); ?></label></dt>
              <dd><input name="city" id="city" size="40"
                      value="<?php echo $city; ?>" />
              </dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label  for="state"><?php echo $this->_('state'); ?></label></dt>
              <dd>
                <input name="state" id="state" size="2" value="<?php echo $state; ?>" />
              </dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label><?= $this->_('zipcode'); ?></label></dt>
            <dd>
              <input name="zip" size="7" value="<?= $zip; ?>" />
            </dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label  for="email"><?php echo $this->_(['email', 'emails', 1]); ?></label></dt>
            <dd><input name="email" id="email" size="40" value="<?php echo $email; ?>" />
            </dd>
          </dl>
          <dl class="fn1-input-field">
            <dt><label  for="phone"><?php echo $this->_('phone'); ?></label></dt>
            <dd><input name="phone" id="phone" size="40" value="<?php echo $phone; ?>" /></dd>
          </dl>
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
