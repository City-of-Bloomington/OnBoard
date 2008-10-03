<?php
require_once 'PHPUnit/Framework.php';

/**
 * Test class for Appointer.
 */
class AppointerTest extends PHPUnit_Framework_TestCase
{
    public function testLoadById()
    {
    	$appointer = new Appointer(1);
    	$this->assertEquals($appointer->getName(),'Elected');
    }

    /**
     * @todo Implement testValidate().
     */
    public function testValidate()
    {
    	# Name Should be required
    	$appointer = new Appointer();
    	try
    	{
    		$appointer->validate();
    		$this->fail('Missing name did not throw an exception');
    	}
    	catch (Exception $e)
    	{
			# Success
    	}
    }

    public function testUpdate()
    {
    	$appointer = new Appointer(1);
    	$appointer->setName('Test');
    	try { $appointer->save(); }
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	$appointer->setName('Elected');
    	$appointer->save();
    }

    public function testInsertAndDelete()
    {
    	$appointer = new Appointer();
    	$appointer->setName('Test Appointer');
    	try
    	{
    		$appointer->save();
    		$id = $appointer->getId();
    		$this->assertGreaterThan(1,$id);
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	try { $appointer->delete(); }
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	try
    	{
			$appointer = new Appointer($id);
			$this->fail('Appointer failed to delete');
    	}
    	catch (Exception $e)
    	{
			# Success
    	}
    }
}
