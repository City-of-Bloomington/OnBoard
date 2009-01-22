<?php
require_once 'PHPUnit/Framework.php';

class AppointerUnitTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
    	// Name should be the only required field
    	$appointer = new Appointer();
    	try
    	{
    		$appointer->validate();
    		$this->fail('Missing name did not throw an exception');
    	}
    	catch (Exception $e)
    	{
			// Success
    	}

    	$appointer->setName('Test Appointer');
    	$appointer->validate();
    }
}