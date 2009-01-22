<?php
require_once 'PHPUnit/Framework.php';

class SeatListDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;
	protected $appointer;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection();

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();
		$this->committee = $committee;

		$appointer = new Appointer();
		$appointer->setName('Seat Test Appointer');
		$appointer->save();
		$this->appointer = $appointer;
	}

	/**
	 * Seats should be ordered alphabetically
	 */
	public function testFind()
	{
		$seat = new Seat();
		$seat->setCommittee($this->committee);
		$seat->setAppointer($this->appointer);
		$seat->setTitle('Test Seat');

		$seat = new Seat();
		$seat->setCommittee($this->committee);
		$seat->setAppointer($this->appointer);
		$seat->setTitle('Another Test Seat');

		$PDO = Database::getConnection();
		$query = $PDO->query('select title from seats order by title');
		$result = $query->fetchAll();

		$list = new SeatList();
		$list->find();
		foreach ($list as $i=>$seat)
		{
			$this->assertEquals($seat->getTitle(),$result[$i]['title']);
		}
	}
}
