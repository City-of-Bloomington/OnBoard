<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
$title = View::escape($this->seat->getTitle());
$appointer = View::escape($this->seat->getAppointer());
$committee = View::escape($this->seat->getCommittee()->getName());


$editRequirementsButton = '';
if (userHasRole(array('Administrator','Clerk')))
{
	$url = new URL(BASE_URL.'/seats/editRequirements.php');
	$url->seat_id = $this->seat->getId();
	$editRequirementsButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
}
echo "
<div class=\"interfaceBox\">
	<h1>$title</h1>
	<table>
	<tr><th>Appointed By</th>
		<td>$appointer</td>
	</tr>
	<tr><th>Committee</th>
		<td>$committee</td>
	</tr>
	<tr><th>Maximum Current Members</th>
		<td>{$this->seat->getMaxCurrentMembers()}</td></tr>
	<tr><th>$editRequirementsButton
			Requirements
		</th>
		<td>
";
			if ($this->seat->hasRequirements())
			{
				echo '<ul>';
				foreach($this->seat->getRequirements() as $requirement)
				{
					echo '<li>'.View::escape($requirement).'</li>';
				}
				echo '</ul>';
			}
echo "
		</td>
	</tr>
	</table>
</div>
";
