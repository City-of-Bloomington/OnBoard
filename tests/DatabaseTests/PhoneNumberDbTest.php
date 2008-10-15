<?php
require_once 'PHPUnit/Framework.php';

class PhoneNumberDbTest extends PHPUnit_Framework_TestCase
{
	protected $user;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");

		$PDO = Database::getConnection();

		$query = $PDO->query('select id from users limit 1');
		$result = $query->fetchAll();
		$this->user = new User($result[0]['id']);
	}

    public function testSaveLoad()
    {
		$phonenumber = new PhoneNumber();
		$phonenumber->setUser($this->user);
		$phonenumber->setNumber('xxx-xxx-xxxx');
    	try
		{
			$phonenumber->save();
			$id = $phonenumber->getId();
			$this->assertGreaterThan(0,$id);
		}
		catch (Exception $e) { $this->fail($e->getMessage()); }

		$phonenumber = new PhoneNumber($id);
		$this->assertEquals($phonenumber->getNumber(),'xxx-xxx-xxxx');

		$phonenumber->setNumber('111-111-1111');
		$phonenumber->save();

		$phonenumber = new PhoneNumber($id);
		$this->assertEquals($phonenumber->getNumber(),'111-111-1111');
    }
}
