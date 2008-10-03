<?php
require_once 'PHPUnit/Framework.php';

class TopicDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;

	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection(true);

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();
		$this->committee = $committee;
	}

}
