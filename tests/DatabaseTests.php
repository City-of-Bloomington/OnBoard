<?php
require_once 'PHPUnit/Framework.php';

require_once 'DatabaseTests/AppointerListTest.php';
require_once 'DatabaseTests/AppointerTest.php';
require_once 'DatabaseTests/CommitteeListTest.php';
require_once 'DatabaseTests/CommitteeTest.php';
require_once 'DatabaseTests/MemberListTest.php';
require_once 'DatabaseTests/MemberTest.php';
require_once 'DatabaseTests/RequirementListTest.php';
require_once 'DatabaseTests/RequirementTest.php';
require_once 'DatabaseTests/RoleListTest.php';
require_once 'DatabaseTests/RoleTest.php';
require_once 'DatabaseTests/SeatListTest.php';
require_once 'DatabaseTests/SeatTest.php';
require_once 'DatabaseTests/TopicListTest.php';
require_once 'DatabaseTests/TopicTest.php';
require_once 'DatabaseTests/TopicTypeListTest.php';
require_once 'DatabaseTests/TopicTypeTest.php';
require_once 'DatabaseTests/UserListTest.php';
require_once 'DatabaseTests/UserTest.php';
require_once 'DatabaseTests/VoteListTest.php';
require_once 'DatabaseTests/VoteTest.php';
require_once 'DatabaseTests/VoteTypeListTest.php';
require_once 'DatabaseTests/VoteTypeTest.php';
require_once 'DatabaseTests/VotingRecordListTest.php';
require_once 'DatabaseTests/VotingRecordTest.php';

class DatabaseTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new DatabaseTests('Committee Manager Classes');

		$suite->addTestSuite('AppointerListTest');
		$suite->addTestSuite('AppointerTest');
		$suite->addTestSuite('CommitteeListTest');
		$suite->addTestSuite('CommitteeTest');
		$suite->addTestSuite('MemberListTest');
		$suite->addTestSuite('MemberTest');
		$suite->addTestSuite('RequirementListTest');
		$suite->addTestSuite('RequirementTest');
		$suite->addTestSuite('RoleListTest');
		$suite->addTestSuite('RoleTest');
		$suite->addTestSuite('SeatListTest');
		$suite->addTestSuite('SeatTest');
		$suite->addTestSuite('TopicListTest');
		$suite->addTestSuite('TopicTest');
		$suite->addTestSuite('TopicTypeListTest');
		$suite->addTestSuite('TopicTypeTest');
		$suite->addTestSuite('UserListTest');
		$suite->addTestSuite('UserTest');
		$suite->addTestSuite('VoteListTest');
		$suite->addTestSuite('VoteTest');
		$suite->addTestSuite('VoteTypeListTest');
		$suite->addTestSuite('VoteTypeTest');
		$suite->addTestSuite('VotingRecordListTest');
		$suite->addTestSuite('VotingRecordTest');

        return $suite;
    }
}
