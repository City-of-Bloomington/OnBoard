<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param User $this->member
 */
$user = $this->member->getUser();

$fields = array('firstname','lastname','email','address','city','zipcode','about');
foreach($fields as $field)
{
	$get = 'get'.ucfirst($field);
	$$field = View::escape($user->$get());
}

echo "
<member id=\"{$this->member->getId()}\">
	<firstname>$firstname</firstname>
	<lastname>$lastname</lastname>
	<email>$email</email>
	<address>$address</address>
	<city>$city</city>
	<zipcode>$zipcode</zipcode>
	<about>$about</about>
";

$this->memberList = $user->getMembers();
include APPLICATION_HOME.'/blocks/xml/members/seatList.inc';
include APPLICATION_HOME.'/blocks/xml/members/memberReport.inc';

$this->user = $user;
include APPLICATION_HOME.'/blocks/xml/members/votingRecord.inc';

echo "
</member>
";
