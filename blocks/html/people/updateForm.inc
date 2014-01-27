<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Application\Models\RaceTable;
use Blossom\Classes\View;

$fields = ['firstname','lastname','email', 'about', 'gender', 'race_id'];
foreach ($fields as $f) {
	$get = 'get'.ucfirst($f);
	$$f  = View::escape($this->person->$get());
}

$title = $this->person->getId() ? View::escape('Edit '.$this->person->getFirstname()) : $this->_('labels.add_person');
?>
<div class="updatePersonForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/people/update">
		<fieldset><legend><?php echo $this->_('labels.info_person'); ?></legend>
			<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if (!in_array($key, $fields)) {
						$key   = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<table>
				<tr><td><label for="firstname" class="required"><?php echo $this->_('labels.firstname'); ?></label></td>
					<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
				</tr>

				<tr><td><label for="lastname" class="required"><?php echo $this->_('labels.lastname'); ?></label></td>
					<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
				</tr>

				<tr><td><label for="email"><?php echo $this->_(['labels.email','labels.emails',1]); ?></label></td>
					<td><input name="email" id="email" value="<?php echo $email; ?>" /></td>
				</tr>

				<tr><td><label for="about"><?php echo $this->_('labels.about'); ?></label></td>
					<td><textarea name="about" id="about" rows="3" cols="60"><?php echo $about; ?></textarea>
					</td>
				</tr>

				<tr><td><label><?php echo $this->_('labels.gender'); ?></label></td>
					<td><label><input name="gender" type="radio" value="male"   <?php if ($gender=='male'  ) echo 'checked="checked"'; ?> /> M</label>
						<label><input name="gender" type="radio" value="female" <?php if ($gender=='female') echo 'checked="checked"'; ?> /> F</label>
					</td>
				</tr>

				<tr><td><label for="race_id"><?php echo $this->_(['labels.race', 'labels.races', 1]); ?></label></td>
					<td><select name="race_id" id="race_id">
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
					</td>
				</tr>

			</table>

			<button type="submit">
				<i class="fa fa-save"></i>
				<?php echo $this->translate('labels.save'); ?>
			</button>
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

				echo "
				<a class=\"btn\" href=\"$return_url\" $onclick>
					<i class=\"fa fa-times\"></i>
					{$this->translate('labels.cancel')}
				</a>
				";
			?>
		</fieldset>
	</form>
</div>
