<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
$fields = array('gender','firstname','lastname','email','address','city',
				'zipcode','about','race_id');
foreach($fields as $field)
{
	$getter = 'get'.ucfirst($field);
	$$field = View::escape($this->user->$getter());
}
?>
<fieldset><legend>Personal Info</legend>
	<table>
	<tr><td><label class="required">Gender</label>
		<td><label><input name="user[gender]" type="radio" value="male" <?php if($gender=='male') echo ' checked="checked"'; ?> />M</label>
			<label><input name="user[gender]" type="radio" value="female" <?php if($gender=='female') echo ' checked="checked"'; ?> />F</label>
		</td>
	</tr>
	<tr><td><label for="user-firstname">Firstname</label></td>
		<td><input name="user[firstname]" id="user-firstname" value="<?php echo $firstname; ?>" /></td></tr>

	<tr><td><label for="user-lastname">Lastname</label></td>
		<td><input name="user[lastname]" id="user-lastname" value="<?php echo $lastname; ?>" /></td></tr>

	<tr><td><label for="user-email">Email</label></td>
		<td><input name="user[email]" id="user-email" value="<?php echo $email; ?>" /></td></tr>

	<tr><td><label for="user-address">Address</label></td>
		<td><input name="user[address]" id="user-address" value="<?php echo $address; ?>" /></td></tr>

	<tr><td><label for="user-city">City</label></td>
		<td><input name="user[city]" id="user-city" value="<?php echo $city; ?>" /></td></tr>

	<tr><td><label for="user-zipcode">Zip</label></td>
		<td><input name="user[zipcode]" id="user-zipcode" value="<?php echo $zipcode; ?>" /></td></tr>

	<tr><td><label for="user-about">About</label></td>
		<td><textarea name="user[about]" id="user-about" rows="3" cols="60"><?php echo $about; ?></textarea></td></tr>

	<tr><td><label for="user-birthdate-mon">Birthdate</label></td>
		<td><select name="user[birthdate][mon]" id="user-birthdate-mon"><option></option>
			<?php
				if ($this->user->getBirthdate())
				{
					$date = getdate($this->user->getBirthdate());
				}
				else { $date = array('year'=>null,'mon'=>null,'mday'=>null); }

				for($i=1; $i<=12; $i++)
				{
					$selected = $i==$date['mon'] ? ' selected="selected"' : '';
					echo "<option$selected>$i</option>";
				}
			?>
			</select>
			<select name="user[birthdate][mday]"><option></option>
			<?php
				for($i=1; $i<=31; $i++)
				{
					$selected = $i==$date['mday'] ? ' selected="selected"' : '';
					echo "<option$selected>$i</option>";
				}
			?>
			</select>
			<input name="user[birthdate][year]" id="user-birthdate-year" size="4" maxlength="4" value="<?php echo $date['year']; ?>" />
		</td>
	</tr>
	<tr><td><label for="user-race_id">Race</label></td>
		<td><select name="user[race_id]" id="user-race_id"><option></option>
			<?php
				$list = new RaceList();
				$list->find();
				foreach($list as $race)
				{
					$selected = $race_id==$race->getId() ? ' selected="selected"' : '';
					echo "<option value=\"{$race->getId()}\"$selected>$race</option>";
				}
			?>
			</select>
		</td>
	</tr>

	</table>
</fieldset>
