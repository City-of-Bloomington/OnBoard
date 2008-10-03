<?php
require_once 'PHPUnit/Framework.php';

require_once 'UnitTests.php';
require_once 'DatabaseTests.php';

class AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new AllTests('Committee Manager');
        $suite->addTest(UnitTests::suite());
        $suite->addTest(DatabaseTests::suite());
        return $suite;
    }
}
