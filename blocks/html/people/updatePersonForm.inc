<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param string $this->title
 */
$fields = array('firstname','lastname','email','address','city','zipcode','about');
$privateFields = $this->person->getPrivateFields();
$privateChecked = array();
foreach ($fields as $field) {
	$getter = 'get'.ucfirst($field);
	$$field = View::escape($this->person->$getter());
	$privateChecked[$field] = in_array($field,$privateFields) ? 'checked="checked"' : '';
}
$privateChecked['gender'] = in_array('gender',$privateFields) ? 'checked="checked"' : '';
$privateChecked['race_id'] = in_array('race_id',$privateFields) ? 'checked="checked"' : '';
$privateChecked['birthdate'] = in_array('birthdate',$privateFields) ? 'checked="checked"' : '';

$title = $this->title ? View::escape($this->title) : 'Update Person';
echo "<h1>$title</h1>";
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Person Info</legend>
		<input name="person_id" type="hidden" value="<?php echo $this->person->getId(); ?>" />
		<table>
			<tr><th>Private</th>
				<th></th>
				<th></th>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="firstname" <?php echo $privateChecked['firstname']; ?> /></td>
				<td><label for="person-firstname" class="required">Firstname</label></td>
				<td><input name="person[firstname]" id="person-firstname" value="<?php echo $firstname; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="lastname" <?php echo $privateChecked['lastname']; ?> /></td>
				<td><label for="person-lastname" class="required">Lastname</label></td>
				<td><input name="person[lastname]" id="person-lastname" value="<?php echo $lastname; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="email" <?php echo $privateChecked['email']; ?> /></td>
				<td><label for="person-email">Email</label></td>
				<td><input name="person[email]" id="person-email" value="<?php echo $email; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="address" <?php echo $privateChecked['address']; ?> /></td>
				<td><label for="person-address">Address</label></td>
				<td><input name="person[address]" id="person-address" value="<?php echo $address; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="city" <?php echo $privateChecked['city']; ?>/></td>
				<td><label for="person-city">City</label></td>
				<td><input name="person[city]" id="person-city" value="<?php echo $city; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="zipcode" <?php echo $privateChecked['zipcode']; ?> /></td>
				<td><label for="person-zipcode">Zip</label></td>
				<td><input name="person[zipcode]" id="person-zipcode" value="<?php echo $zipcode; ?>" />
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="about" <?php echo $privateChecked['about']; ?> /></td>
				<td><label for="person-about">About</label></td>
				<td><textarea name="person[about]" id="person-about" rows="3" cols="60"><?php echo $about; ?></textarea>
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="gender" <?php echo $privateChecked['gender']; ?> /></td>
				<td><label>Gender</label></td>
				<td><label>
						<input name="person[gender]" type="radio" value="male" <?php if($this->person->getGender()=='male') echo 'checked="checked"'; ?> />
						M
					</label>
					<label>
						<input name="person[gender]" type="radio" value="female" <?php if($this->person->getGender()=='female') echo 'checked="checked"'; ?> />
						F
					</label>
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="race_id" <?php echo $privateChecked['race_id']; ?> /></td>
				<td><label for="person-race_id">Race</label></td>
				<td><select name="person[race_id]" id="person-race_id">
						<option></option>
						<?php
							$list = new RaceList();
							$list->find();
							foreach ($list as $race) {
								$selected = $this->person->getRace_id()==$race->getId() ? ' selected="selected"' : '';
								echo "<option value=\"{$race->getId()}\"$selected>$race</option>";
							}
						?>
					</select>
				</td>
			</tr>

			<tr><td><input name="person[privateFields][]" type="checkbox" value="birthdate" <?php echo $privateChecked['birthdate']; ?> /></td>
				<td><label for="person-birthdate">Birthdate</label></td>
				<td><input name="person[birthdate]" id="person-birthdate" value="<?php echo $this->person->getBirthdate('n/j/Y'); ?>" />
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/people';">
			Cancel
		</button>
	</fieldset>
</form>
