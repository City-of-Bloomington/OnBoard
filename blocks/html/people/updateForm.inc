<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\RaceTable;

$fields = ['firstname','lastname','email', 'about', 'gender', 'race_id'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f  = self::escape($this->person->$get());
}

$title = $this->person->getId() ? self::escape('Edit '.$this->person->getFirstname()) : $this->_('add_person');
?>
<section class="updatePersonForm">
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URI; ?>/people/update">
		<fieldset><legend><?php echo $this->_('info_person'); ?></legend>
			<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if (!in_array($key, $fields)) {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
            <dl class="fn1-input-field">
                <dt><label for="firstname" class="required"><?php echo $this->_('firstname'); ?></label></dt>
                <dd><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="lastname" class="required"><?php echo $this->_('lastname'); ?></label></dt>
                <dd><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="email"><?php echo $this->_(['email','emails',1]); ?></label></dt>
                <dd><input name="email" id="email" value="<?php echo $email; ?>" /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label><?php echo $this->_('gender'); ?></label></dt>
                <dd><label><input name="gender" type="radio" value="male"   <?php if ($gender=='male'  ) echo 'checked="checked"'; ?> /> M</label>
                    <label><input name="gender" type="radio" value="female" <?php if ($gender=='female') echo 'checked="checked"'; ?> /> F</label>
                </dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="race_id"><?php echo $this->_(['race', 'races', 1]); ?></label></dt>
                <dd><select name="race_id" id="race_id">
                        <option></option>
                        <?php
                            $table = new RaceTable();
                            $list = $table->find();
                            foreach ($list as $race) {
                                $selected = $race_id==$race->getId() ? ' selected="selected"' : '';
                                echo "<option value=\"{$race->getId()}\"$selected>$race</option>";
                            }
                        ?>
                    </select>
                </dd>
            </dl>
        </fieldset>

        <fieldset><legend><?= $this->_('about'); ?></legend>
            <textarea name="about" id="about" rows="3" cols="60"><?php echo $about; ?></textarea>
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
