<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\Person;
use Application\Models\RaceTable;

$fields = ['about', 'gender', 'race_id', 'address', 'city', 'state', 'zip'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f  = self::escape($this->person->$get());
}

$title = $this->person->getId() ? $this->_('person_edit') : $this->_('person_add');
?>
<section class="updatePersonForm">
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URI; ?>/people/update">
		<fieldset><legend><?= $this->_('info_person'); ?></legend>
			<input name="person_id" type="hidden" value="<?= $this->person->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if (!in_array($key, $fields)) {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}

				$this->_include('people/partials/personalInfoFields.inc');

                $field = $this->template->getHelper('field');
			?>
            <dl class="fn1-input-field">
                <dt><label><?= $this->_('gender'); ?></label></dt>
                <dd><label><input name="gender" type="radio" value="male"   <?php if ($gender==='male'  ) echo 'checked="checked"'; ?> /> M</label>
                    <label><input name="gender" type="radio" value="female" <?php if ($gender==='female') echo 'checked="checked"'; ?> /> F</label>
                </dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label   for="race_id"><?= $this->_('race'); ?></label></dt>
                <dd><select name="race_id" id="race_id">
                        <option></option>
                        <?php
                            $table = new RaceTable();
                            $list = $table->find();
                            foreach ($list as $race) {
                                $selected = $race_id===$race->getId() ? ' selected="selected"' : '';
                                echo "<option value=\"{$race->getId()}\"$selected>$race</option>";
                            }
                        ?>
                    </select>
                </dd>
            </dl>
        </fieldset>

        <fieldset><legend><?= $this->_('address'); ?></legend>
            <textarea name="address" id="address"></textarea>
            <?= $field->field(['name'=>'city', 'id'=>'city', 'label'=>$this->_('city'), 'value'=>$city]); ?>
            <dl class="fn1-input-field--select">
                <dt><label   for="state"><?= $this->_('state'); ?></label></dt>
                <dd><select name="state" id="state"><option value=""></option>
                    <?php
                        foreach (Person::$STATES as $s) {
                            $selected = $state === $s ? 'selected="true"' : '';
                            echo "<option $selected>$s</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <?= $field->field(['name'=>'zip',  'id'=>'zip',  'label'=>$this->_('zip'),  'value'=>$zip ]); ?>
        </fieldset>

        <fieldset><legend><?= $this->_('about'); ?></legend>
            <textarea name="about" id="about"><?= $about; ?></textarea>
        </fieldset>

        <fieldset>
			<?php
				// Set the correct URL for the cancel button
				if (isset($_REQUEST['return_url'])) {
					$return_url = urlencode($_REQUEST['return_url']);
				}
				elseif ($this->person->getId()) {
					$return_url = $this->person->getUrl();
				}
				else {
					$return_url = BASE_URI.'/people';
				}

				$onclick = isset($_REQUEST['callback'])
					? 'onclick="self.close();"'
					: '';

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($return_url, $onclick);
			?>
		</fieldset>
	</form>
</section>
