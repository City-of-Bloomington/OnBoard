<?php
require_once 'PHPUnit/Framework.php';

/**
 * Test class for Appointer.
 */
class AppointerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

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

    public function testInsert()
    {
    	$appointer = new Appointer();
    	$appointer->setName('Test Appointer');
    	try
    	{
    		$appointer->save();
    		$this->assertGreaterThan(1,$appointer->getId());
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }
    }

    /**
     * @todo Implement testDelete().
     */
    public function testDelete()
    {
    	$appointer = new Appointer(6);
    	try { $appointer->delete(); }
    	catch (Exception $e) { $this->fail($e->getMessage()); }
    }
}
