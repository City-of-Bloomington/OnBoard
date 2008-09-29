<?php
require_once 'PHPUnit/Framework.php';

require_once 'ClassTests.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Committee Manager');
        $suite->addTest(ClassTests::suite());
        return $suite;
    }
}
