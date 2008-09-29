<?php
require_once 'PHPUnit/Framework.php';

require_once 'ClassTests/AppointerListTest.php';
require_once 'ClassTests/AppointerTest.php';
require_once 'ClassTests/CommitteeListTest.php';
require_once 'ClassTests/CommitteeTest.php';
require_once 'ClassTests/MemberListTest.php';
require_once 'ClassTests/MemberTest.php';
require_once 'ClassTests/RequirementListTest.php';
require_once 'ClassTests/RequirementTest.php';
require_once 'ClassTests/RoleListTest.php';
require_once 'ClassTests/RoleTest.php';
require_once 'ClassTests/SeatListTest.php';
require_once 'ClassTests/SeatTest.php';
require_once 'ClassTests/TopicListTest.php';
require_once 'ClassTests/TopicTest.php';
require_once 'ClassTests/TopicTypeListTest.php';
require_once 'ClassTests/TopicTypeTest.php';
require_once 'ClassTests/UserListTest.php';
require_once 'ClassTests/UserTest.php';
require_once 'ClassTests/VoteListTest.php';
require_once 'ClassTests/VoteTest.php';
require_once 'ClassTests/VoteTypeListTest.php';
require_once 'ClassTests/VoteTypeTest.php';
require_once 'ClassTests/VotingRecordListTest.php';
require_once 'ClassTests/VotingRecordTest.php';

class ClassTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Committee Manager Classes');

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
