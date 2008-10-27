<?php
require_once 'PHPUnit/Framework.php';

class UserDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
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
			$phoneNumber['private'] = 1;
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
			$this->assertTrue($phoneNumber->isPrivate());

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
			$this->assertFalse($phoneNumber->isPrivate());
		}
	}

	public function testSavePrivateFields()
	{
		$user = new User();
		$user->setFirstname('Test');
		$user->setLastname('User');
		$user->setGender('Male');

		$privateFields = array('gender','firstname','lastname','address','email');
		$publicFields = array('city','zipcode','about');

		$user->setPrivateFields($privateFields);
		$user->save();
		$id = $user->getId();

		$user = new User($id);
		foreach($privateFields as $field)
		{
			$this->assertTrue($user->isPrivate($field));
		}
		foreach($publicFields as $field)
		{
			$this->assertTrue(!$user->isPrivate($field));
		}
	}

	public function testAdministratorCanSeePrivateFields()
	{
		$PDO = Database::getConnection();
		$sql = 'select user_id from user_roles left join roles on role_id=id where name=? limit 1';
		$query = $PDO->prepare($sql);
		$query->execute(array('Administrator'));
		$result = $query->fetchAll();
		if (count($result))
		{
			$_SESSION['USER'] = new User($result[0]['user_id']);
			$user = new User();
			$user->setFirstname('Test');
			$user->setLastname('User');
			$user->setPrivateFields(array('firstname','lastname'));

			$this->assertEquals($user->getFirstname(),'Test');
			$this->assertEquals($user->getLastname(),'User');
		}
	}

	public function testClerkCanSeePrivateFields()
	{
		$PDO = Database::getConnection();
		$sql = 'select user_id from user_roles left join roles on role_id=id where name=? limit 1';
		$query = $PDO->prepare($sql);
		$query->execute(array('Clerk'));
		$result = $query->fetchAll();
		if (count($result))
		{
			$_SESSION['USER'] = new User($result[0]['user_id']);
			$user = new User();
			$user->setFirstname('Test');
			$user->setLastname('User');
			$user->setPrivateFields(array('firstname','lastname'));

			$this->assertEquals($user->getFirstname(),'Test');
			$this->assertEquals($user->getLastname(),'User');
		}
	}

	public function testClearAllRoles()
	{
		$user = new User();
		$user->setFirstname('Test');
		$user->setLastname('User');
		$user->setRoles(array('Clerk'));
		$user->save();
		$id = $user->getId();

		$user = new User($id);
		$this->assertTrue($user->hasRole('Clerk'));

		echo "Trying to clear roles\n";
		$user->setRoles(null);
		print_r($user->getRoles());
		$user->save();

		$user = new User($id);
		$this->assertFalse($user->hasRole('Clerk'));
	}
}
