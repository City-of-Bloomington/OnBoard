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

	public function testSetPrivateFields()
	{
		$user = new User();

		$privateFields = array('gender','firstname','lastname','address','email');
		foreach($privateFields as $field)
		{
			$this->assertFalse($user->isPrivate($field));
		}

		$publicFields = array('city','zipcode','about');
		foreach($publicFields as $field)
		{
			$this->assertFalse($user->isPrivate($field));
		}

		$user->setPrivateFields($privateFields);
		foreach($privateFields as $field)
		{
			$this->assertTrue($user->isPrivate($field));
		}
		foreach($publicFields as $field)
		{
			$this->assertFalse($user->isPrivate($field));
		}
	}

	public function testPrivateFieldsRestrictions()
	{
		$user = new User();
		$user->setPrivateFields(array('firstname','lastname'));
		$user->setFirstname('Test');
		$user->setLastname('User');

		$this->assertTrue($user->getFirstname()!='Test');
		$this->assertTrue($user->getLastname()!='User');
	}
}
