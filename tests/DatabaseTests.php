<?php
require_once 'PHPUnit/Framework.php';

require_once 'DatabaseTests/AppointerListDbTest.php';
require_once 'DatabaseTests/AppointerDbTest.php';
require_once 'DatabaseTests/CommitteeListDbTest.php';
require_once 'DatabaseTests/CommitteeDbTest.php';
require_once 'DatabaseTests/MemberListDbTest.php';
require_once 'DatabaseTests/MemberDbTest.php';
require_once 'DatabaseTests/PhoneNumberDbTest.php';
require_once 'DatabaseTests/PhoneNumberListDbTest.php';
require_once 'DatabaseTests/RaceDbTest.php';
require_once 'DatabaseTests/RaceListDbTest.php';
require_once 'DatabaseTests/RequirementListDbTest.php';
require_once 'DatabaseTests/RequirementDbTest.php';
require_once 'DatabaseTests/RoleListDbTest.php';
require_once 'DatabaseTests/RoleDbTest.php';
require_once 'DatabaseTests/SeatListDbTest.php';
require_once 'DatabaseTests/SeatDbTest.php';
require_once 'DatabaseTests/TopicListDbTest.php';
require_once 'DatabaseTests/TopicDbTest.php';
require_once 'DatabaseTests/TopicTypeListDbTest.php';
require_once 'DatabaseTests/TopicTypeDbTest.php';
require_once 'DatabaseTests/UserListDbTest.php';
require_once 'DatabaseTests/UserDbTest.php';
require_once 'DatabaseTests/VoteListDbTest.php';
require_once 'DatabaseTests/VoteDbTest.php';
require_once 'DatabaseTests/VoteTypeListDbTest.php';
require_once 'DatabaseTests/VoteTypeDbTest.php';
require_once 'DatabaseTests/VotingRecordListDbTest.php';
require_once 'DatabaseTests/VotingRecordDbTest.php';

class DatabaseTests extends PHPUnit_Framework_TestSuite
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

	protected function tearDown()
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
        $suite = new DatabaseTests('Committee Manager Classes');

		$suite->addTestSuite('AppointerListDbTest');
		$suite->addTestSuite('AppointerDbTest');
		$suite->addTestSuite('CommitteeListDbTest');
		$suite->addTestSuite('CommitteeDbTest');
		$suite->addTestSuite('MemberListDbTest');
		$suite->addTestSuite('MemberDbTest');
		$suite->addTestSuite('PhoneNumberDbTest');
		$suite->addTestSuite('PhoneNumberListDbTest');
		$suite->addTestSuite('RaceDbTest');
		$suite->addTestSuite('RaceListDbTest');
		$suite->addTestSuite('RequirementListDbTest');
		$suite->addTestSuite('RequirementDbTest');
		$suite->addTestSuite('RoleListDbTest');
		$suite->addTestSuite('RoleDbTest');
		$suite->addTestSuite('SeatListDbTest');
		$suite->addTestSuite('SeatDbTest');
		$suite->addTestSuite('TopicListDbTest');
		$suite->addTestSuite('TopicDbTest');
		$suite->addTestSuite('TopicTypeListDbTest');
		$suite->addTestSuite('TopicTypeDbTest');
		$suite->addTestSuite('UserListDbTest');
		$suite->addTestSuite('UserDbTest');
		$suite->addTestSuite('VoteListDbTest');
		$suite->addTestSuite('VoteDbTest');
		$suite->addTestSuite('VoteTypeListDbTest');
		$suite->addTestSuite('VoteTypeDbTest');
		$suite->addTestSuite('VotingRecordListDbTest');
		$suite->addTestSuite('VotingRecordDbTest');

        return $suite;
    }
}
