<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class SeatTable extends TableGateway
{
	public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

	public function find($fields=null, $order='startDate desc', $paginated=false, $limit=null)
	{
		$select = new Select('seats');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
						$select->where("startDate<='$date'");
						$select->where("(endDate is null or endDate>='$date')");
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
