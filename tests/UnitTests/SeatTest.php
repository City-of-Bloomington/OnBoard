<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Seat;
use Application\Models\Term;

require_once '../../configuration.inc';

class SeatTest extends PHPUnit_Framework_TestCase
{
    public static $DATE_FORMAT = 'Y-m-d';

    public function futureTermDateProvider()
    {
        return [
            [['start'=>'2013-02-01', 'end'=>'2015-01-31', 'target'=>'2016-01-15', 'length'=>'P2Y'] , ['start'=>'2015-02-01', 'end'=>'2017-01-31']],
            [['start'=>'2011-02-01', 'end'=>'2013-01-31', 'target'=>'2016-01-15', 'length'=>'P2Y'] , ['start'=>'2015-02-01', 'end'=>'2017-01-31']]
        ];
    }


	/**
	 * @dataProvider futureTermDateProvider
	 */
    public function testGenerateFutureTerms($in, $out)
    {
        $seat = new Seat();
        $seat->setTermLength($in['length']);

        $latestTerm = new Term();
        $latestTerm->setSeat($seat);
        $latestTerm->setStartDate($in['start'], self::$DATE_FORMAT);
        $latestTerm->setEndDate  ($in['end'],   self::$DATE_FORMAT);

        $timestamp = strtotime($in['target']);

        $term = $seat->generateTermForTimestamp($latestTerm, $timestamp);

        $this->assertEquals($term->getStartDate(self::$DATE_FORMAT), $out['start']);
        $this->assertEquals($term->getEndDate  (self::$DATE_FORMAT), $out['end']);
    }

}