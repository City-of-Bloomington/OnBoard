<?php
require_once 'PHPUnit/Framework.php';

require_once 'DatabaseTests.php';

class AllTests extends PHPUnit_Framework_TestSuite
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/testData.sql\n");

		$PDO = Database::getConnection(true);
	}

    public static function suite()
    {
        $suite = new AllTests('Committee Manager');
        $suite->addTest(DatabaseTests::suite());
        return $suite;
    }
}
