<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Person</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Person Info</legend>
		<table>

			<tr><td><label for="person-firstname" class="required">Firstname</label></td>
				<td><input name="person[firstname]" id="person-firstname" />
				</td>
			</tr>

			<tr><td><label for="person-lastname" class="required">Lastname</label></td>
				<td><input name="person[lastname]" id="person-lastname" />
				</td>
			</tr>

			<tr><td><label for="person-email">Email</label></td>
				<td><input name="person[email]" id="person-email" />
				</td>
			</tr>

			<tr><td><label for="person-address">Address</label></td>
				<td><input name="person[address]" id="person-address" />
				</td>
			</tr>

			<tr><td><label for="person-city">City</label></td>
				<td><input name="person[city]" id="person-city" />
				</td>
			</tr>

			<tr><td><label for="person-zipcode">Zip</label></td>
				<td><input name="person[zipcode]" id="person-zipcode" />
				</td>
			</tr>

			<tr><td><label for="person-about">About</label></td>
				<td><textarea name="person[about]" id="person-about" rows="3" cols="60"></textarea>
				</td>
			</tr>

			<tr><td><label>Gender</label></td>
				<td><label>
						<input name="person[gender]" type="radio" value="male" checked="checked" />
						M
					</label>
					<label>
						<input name="person[gender]" type="radio" value="female" />
						F
					</label>
				</td>
			</tr>

			<tr><td><label for="person-race_id">Race</label></td>
				<td><select name="person[race_id]" id="person-race_id">
					<?php
						$list = new RaceList();
						$list->find();
						foreach ($list as $race) {
							echo "<option value=\"{$race->getId()}\">$race</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr><td><label for="person-birthdate-mon">Birthdate</label></td>
				<td><select name="person[birthdate][mon]" id="person-birthdate-mon">
						<option></option>
						<?php
							$now = getdate();
							for ($i=1; $i<=12; $i++) {
								$selected = ($i==$now['mon']) ? 'selected="selected"' : '';
								echo "<option $selected>$i</option>";
							}
						?>
					</select>
					<select name="person[birthdate][mday]">
						<option></option>
						<?php
							for ($i=1; $i<=31; $i++) {
								$selected = ($i==$now['mday']) ? 'selected="selected"' : '';
								echo "<option $selected>$i</option>";
							}
						?>
					</select>
					<input name="person[birthdate][year]" id="person-birthdate-year" size="4" maxlength="4" value="<?php echo $now['year']; ?>" />
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/people';">
			Cancel
		</button>
	</fieldset>
</form>
