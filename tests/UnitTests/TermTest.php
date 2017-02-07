<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Seat;
use Application\Models\Term;

require_once '../../bootstrap.inc';

class TermTest extends PHPUnit_Framework_TestCase
{
    public function testSetDates()
    {
        $term = new Term();
        $term->setStartDate('2015-02-01');

        $this->assertEquals('2/1/2015', $term->getStartDate(DATE_FORMAT));
    }

    public function testGenerateNextTerm()
    {
        $seat = new Seat();
        $seat->setTermLength('P2Y');

        $term = new Term();
        $term->setSeat($seat);
        $term->setStartDate('2015-02-01');
        $term->setEndDate  ('2017-01-31');

        $newTerm = $term->generateNextTerm();
        $this->assertEquals('2/1/2017',  $newTerm->getStartDate(DATE_FORMAT));
        $this->assertEquals('1/31/2019', $newTerm->getEndDate  (DATE_FORMAT));
    }

    public function testGeneratePreviousTerm()
    {
        $seat = new Seat();
        $seat->setTermLength('P2Y');

        $term = new Term();
        $term->setSeat($seat);
        $term->setStartDate('2015-02-01');
        $term->setEndDate  ('2017-01-31');

        $newTerm = $term->generatePreviousTerm();
        $this->assertEquals('2/1/2013',  $newTerm->getStartDate(DATE_FORMAT));
        $this->assertEquals('1/31/2015', $newTerm->getEndDate  (DATE_FORMAT));
    }
}
