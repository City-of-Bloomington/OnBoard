<?php
require_once 'PHPUnit/Framework.php';

class UserDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection(true);
	}

	public function testSetPhoneNumbers()
	{
		$user = new User();
		$user->setFirstname('Test');
		$user->setLastname('User');
		$user->setGender('Male');

		$phoneNumbers = array();
		for($i=0; $i<=3; $i++)
		{
			$phoneNumber['ordering'] = '';
			$phoneNumber['number'] = "$i$i$i-$i$i$i-$i$i$i$i";
			$phoneNumber['type'] = '';
			$phoneNumbers[] = $phoneNumber;
		}

		$user->setPhoneNumbers($phoneNumbers);
		$user->save();
		$id = $user->getId();

		$user = new User($id);
		$this->assertEquals(count($user->getPhoneNumbers()),count($phoneNumbers));

		$phoneNumbers = array();
		foreach($user->getPhoneNumbers() as $phoneNumber)
		{
			$number['ordering'] = $phoneNumber->getOrdering();
			$number['number'] = $phoneNumber->getNumber();
			$number['type'] = 'cell';
			$phoneNumbers[] = $number;
		}

		$user->setPhoneNumbers($phoneNumbers);
		$user->save();

		$user = new User($id);
		foreach($user->getPhoneNumbers() as $phoneNumber)
		{
			$this->assertEquals($phoneNumber->getType(),'cell');
		}
	}
}
