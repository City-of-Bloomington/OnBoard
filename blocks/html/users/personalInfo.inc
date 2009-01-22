<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->user
 */
$fields = array('fullname','email','address','city','zipcode','about');
foreach ($fields as $field)
{
	$get = 'get'.ucfirst($field);
	$$field = View::escape($this->user->$get());
}
echo "
<h1>$fullname</h1>
<table>
<tr><th>Email</th>
	<td><a href=\"mailto:$email\">$email</a></td></tr>
<tr><th>Address</th>
	<td>$address</td></tr>
<tr><th>City</th>
	<td>$city</td></tr>
<tr><th>Zip</th>
	<td>$zipcode</td></tr>
</table>
<p>$about</p>
";