<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
?>
<fieldset><legend>Phone Numbers</legend>
	<table>
	<tr><th>Order</th>
		<th>Number</th>
		<th>Type</th>
		<th>Private</th>
	</tr>
	<?php
		$phoneNumbers = $this->user->getPhoneNumbers();
		for($i=0; $i<=3; $i++)
		{
			$ordering = isset($phoneNumbers[$i]) ? $phoneNumbers[$i]->getOrdering() : '';
			$number = isset($phoneNumbers[$i]) ? $phoneNumbers[$i]->getNumber() : '';
			$type = isset($phoneNumbers[$i]) ? $phoneNumbers[$i]->getType() : '';
			$checked = (isset($phoneNumbers[$i]) && $phoneNumbers[$i]->isPrivate()) ? 'checked="checked"' : '';

			echo "
			<tr><td><input name=\"user[phoneNumbers][$i][ordering]\" size=\"2\" maxlength=\"2\" value=\"$ordering\" /></td>
				<td><input name=\"user[phoneNumbers][$i][number]\" size=\"15\" value=\"$number\" /></td>
				<td><select name=\"user[phoneNumbers][$i][type]\">
						<option></option>
			";
					$types = array('home','work','cell','fax');
					foreach($types as $type)
					{
						if (isset($phoneNumbers[$i]) && $phoneNumbers[$i]->getType()==$type)
						{
							echo "<option selected=\"selected\">$type</option>";
						}
						else { echo "<option>$type</option>"; }
					}
			echo "
					</select>
				</td>
				<td><input type=\"checkbox\" name=\"user[phoneNumbers][$i][private]\" value=\"1\" $checked /></td>
			</tr>
			";
		}
	?>
	</table>
</fieldset>