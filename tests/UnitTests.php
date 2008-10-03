<?php
require_once 'PHPUnit/Framework.php';

require_once 'UnitTests/MemberTest.php';

class UnitTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new UnitTests('Committee Manager Unit Tests');

		$suite->addTestSuite('MemberTest');

		return $suite;
	}
}