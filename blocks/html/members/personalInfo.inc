<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->member
 */
$user = $this->member->getUser();

$fields = array('fullname','email','address','city','zipcode','about');
foreach ($fields as $field)
{
	$get = 'get'.ucfirst($field);
	$$field = View::escape($user->$get());
}

echo "<h1>$fullname</h1>";
if ($email || $address || $city || $zipcode)
{
	echo "<table>";
	if ($email)
	{
		echo "<tr><th>Email</th><td><a href=\"mailto:$email\">$email</a></td></tr>";
	}
	if ($address)
	{
		echo "<tr><th>Address</th><td>$address</td></tr>";
	}
	if ($city)
	{
		echo "<tr><th>City</th><td>$city</td></tr>";
	}
	if($zipcode)
	{
		echo "<tr><th>Zip</th><td>$zipcode</td></tr>";
	}
	echo "</table>";
}
if ($about) { echo "<p>$about</p>"; }

$this->memberList = $user->getMembers();
include APPLICATION_HOME.'/blocks/html/members/seatList.inc';

if ($this->member->hasVotingRecord())
{
	include APPLICATION_HOME.'/blocks/html/members/memberReport.inc';

	$this->user = $user;
	include APPLICATION_HOME.'/blocks/html/members/votingRecord.inc';
}
