<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$fields = array('firstname','lastname','email','address','city','zipcode',
				'homePhone','workPhone','about','photoPath');
foreach($fields as $field)
{
	$$field = isset($_POST['user'][$field]) ? View::escape($_POST['user'][$field]) : '';
	if (isset($this->user))
	{
		$getter = 'get'.ucfirst($field);
		$$field = $$field ? $$field : View::escape($this->user->$getter());
	}
}
?>
<fieldset><legend>Personal Info</legend>
	<table>
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

	<tr><td><label for="user-homePhone">Home Phone</label></td>
		<td><input name="user[homePhone]" id="user-homePhone" value="<?php echo $homePhone; ?>" /></td></tr>

	<tr><td><label for="user-workPhone">Work Phone</label></td>
		<td><input name="user[workPhone]" id="user-workPhone" value="<?php echo $workPhone; ?>" /></td></tr>

	<tr><td><label for="user-about">About</label></td>
		<td><textarea name="user[about]" id="user-about" rows="3" cols="60"><?php echo $about; ?></textarea></td></tr>

	<tr><td><label for="user-photoPath">photoPath</label></td>
		<td><input name="user[photoPath]" id="user-photoPath" value="<?php echo $photoPath; ?>" /></td></tr>

	</table>
</fieldset>
