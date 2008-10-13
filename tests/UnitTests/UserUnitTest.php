<?php
require_once 'PHPUnit/Framework.php';

class UserUnitTest extends PHPUnit_Framework_TestCase
{
	public function testSetPhoneNumbers()
	{
		$phoneNumbers = array();
		for($i=0; $i<=3; $i++)
		{
			$phoneNumber['ordering'] = '';
			$phoneNumber['number'] = "$i$i$i-$i$i$i-$i$i$i$i";
			$phoneNumber['type'] = '';
			$phoneNumbers[] = $phoneNumber;
		}

		$user = new User();
		$user->setPhoneNumbers($phoneNumbers);

		foreach($user->getPhoneNumbers() as $i=>$phoneNumber)
		{
			$this->assertEquals($phoneNumber->getNumber(),"$i$i$i-$i$i$i-$i$i$i$i");
		}
	}
}
