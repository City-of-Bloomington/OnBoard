<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Term;

require_once '../../configuration.inc';

class TermTest extends PHPUnit_Framework_TestCase
{
    public function testSetDates()
    {
        $term = new Term();
        $term->setStartDate('2/1/2015', DATE_FORMAT);

        $this->assertEquals('2/1/2015', $term->getStartDate(DATE_FORMAT));
    }
}