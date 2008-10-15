<?php
require_once 'PHPUnit/Framework.php';

require_once 'UnitTests/AppointerUnitTest.php';
require_once 'UnitTests/MemberUnitTest.php';
require_once 'UnitTests/PhoneNumberUnitTest.php';
require_once 'UnitTests/RaceUnitTest.php';
require_once 'UnitTests/RequirementUnitTest.php';
require_once 'UnitTests/RoleUnitTest.php';
require_once 'UnitTests/UserUnitTest.php';

class UnitTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new UnitTests('Committee Manager Unit Tests');

		$suite->addTestSuite('AppointerUnitTest');
		$suite->addTestSuite('MemberUnitTest');
		$suite->addTestSuite('PhoneNumberUnitTest');
		$suite->addTestSuite('RaceUnitTest');
		$suite->addTestSuite('RequirementUnitTest');
		$suite->addTestSuite('RoleUnitTest');
		$suite->addTestSuite('UserUnitTest');

		return $suite;
	}
}