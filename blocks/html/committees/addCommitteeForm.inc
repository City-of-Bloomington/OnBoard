<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Add Committee</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Committee</legend>
		<table>
			<tr><td><label for="committee-name" class="required">Name</label></td>
				<td><input name="committee[name]" id="committee-name" /></td>
			</tr>

			<tr><td><label for="committee-dateFormed-mon">Date Formed</label></td>
				<td><select name="committee[dateFormed][mon]" id="committee-dateFormed-mon">
						<option></option>
						<?php
							for($i=1; $i<=12; $i++) {
								echo "<option>$i</option>";
							}
						?>
					</select>
					<select name="committee[dateFormed][mday]">
						<option></option>
						<?php
							for($i=1; $i<=31; $i++) {
								echo "<option>$i</option>";
							}
						?>
					</select>
					<input name="committee[dateFormed][year]" id="committee-dateFormed-year" size="4" maxlength="4" />
				</td>
			</tr>

			<tr><td><label for="committee-statutoryName">Statutory Name</label></td>
				<td><input name="committee[statutoryName]" id="committee-statutoryName" size="40" />
				</td>
			</tr>

			<tr><td><label for="committee-statuteReference">Statute Reference</label></td>
				<td><input name="committee[statuteReference]" id="committee-statuteReference" size="40" />
				</td>
			</tr>

			<tr><td><label for="committee-website">Website</label></td>
				<td><input name="committee[website]" id="committee-website" size="40" />
				</td>
			</tr>

			<tr><td colspan="2">
					<div><label for="committee-description">Description</label></div>
					<textarea name="committee[description]" id="committee-description" rows="3" cols="60"></textarea>
				</td>
			</tr>
		</table>

		<button type="submit" class="submit">Submit</button>
		<button type="button" class="cancel" onclick="document.location.href='<?php echo BASE_URL; ?>/committees';">
			Cancel
		</button>
	</fieldset>
</form>
