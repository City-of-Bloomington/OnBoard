<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\RaceTable;
use PHPUnit\Framework\TestCase;

class RaceTableTest extends TestCase
{
	public function testRaceTable()
	{
		$table = new RaceTable();
		$results = $table->find();

		$this->assertGreaterThan(0, $results->count());

		foreach ($results as $race) {
			$this->assertEquals('Application\Models\Race', get_class($race));
		}
	}
}
