<?php
require_once 'PHPUnit/Framework.php';

require_once 'TestCases/AppointerListTest.php';
require_once 'TestCases/AppointerTest.php';
require_once 'TestCases/CommitteeListTest.php';
require_once 'TestCases/CommitteeTest.php';
require_once 'TestCases/MemberListTest.php';
require_once 'TestCases/MemberTest.php';
require_once 'TestCases/RequirementListTest.php';
require_once 'TestCases/RequirementTest.php';
require_once 'TestCases/RoleListTest.php';
require_once 'TestCases/RoleTest.php';
require_once 'TestCases/SeatListTest.php';
require_once 'TestCases/SeatTest.php';
require_once 'TestCases/TopicListTest.php';
require_once 'TestCases/TopicTest.php';
require_once 'TestCases/TopicTypeListTest.php';
require_once 'TestCases/TopicTypeTest.php';
require_once 'TestCases/UserListTest.php';
require_once 'TestCases/UserTest.php';
require_once 'TestCases/VoteListTest.php';
require_once 'TestCases/VoteTest.php';
require_once 'TestCases/VoteTypeListTest.php';
require_once 'TestCases/VoteTypeTest.php';
require_once 'TestCases/VotingRecordListTest.php';
require_once 'TestCases/VotingRecordTest.php';

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
