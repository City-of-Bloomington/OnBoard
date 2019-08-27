<?php
/**
 * @copyright 2014-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\RaceTable;
use PHPUnit\Framework\TestCase;

class RaceTableTest extends TestCase
{
	public function testRaceTable()
	{
		$table = new RaceTable();
		$results = $table->find();

		$this->assertGreaterThan(0, $results->count(), 'The races table is empty');

		foreach ($results as $race) {
			$this->assertEquals(
                'Application\Models\Race',
                get_class($race),
                "$race does not extend Models\Race"
                );
		}
	}
}
