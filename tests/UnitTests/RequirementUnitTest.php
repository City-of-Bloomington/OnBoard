<?php
require_once 'PHPUnit/Framework.php';

class RequirementUnitTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
    	# Text should be the only required field
    	$requirement = new Requirement();
    	try
    	{
    		$requirement->validate();
    		$this->fail('Missing text did not throw an exception');
    	}
    	catch (Exception $e)
    	{
			# Success
    	}

    	$requirement->setText('Test Requirement');
    	$requirement->validate();
    }
}