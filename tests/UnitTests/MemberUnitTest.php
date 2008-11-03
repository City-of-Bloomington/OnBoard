<?php
require_once 'PHPUnit/Framework.php';

class MemberUnitTest extends PHPUnit_Framework_TestCase
{
	public function testSetTerm_start()
	{
		$now = time();

		$array = getdate($now);

		$member = new Member();
		$member->setTerm_end($now);
		$this->assertEquals($member->getTerm_end(),$now);
	}
}