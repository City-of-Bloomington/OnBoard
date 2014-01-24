<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once '../../configuration.inc';

class RaceTableTest extends PHPUnit_Framework_TestCase
{
	public function testRaceTable()
	{
		$table = new Application\Models\RaceTable();
		$results = $table->find();

		$this->assertGreaterThan(0, $results->count());

		foreach ($results as $race) {
			$this->assertEquals('Application\Models\Race', get_class($race));
		}
	}
}
