<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Literal;

class SeatTable extends TableGateway
{
	public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

	public function find($fields=null, $order='s.name', $paginated=false, $limit=null)
	{
		$select = new Select(['s'=>'seats']);
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
						$select->where("(s.startDate is null or s.startDate<='$date')");
						$select->where("(s.endDate   is null or s.endDate>='$date')");

						// If they want to order by committee, we have to join the committees table
						if (  (is_array($order) && in_array('c.name', $order))
                            || false !== strpos($order, 'c.name')) {
                            $select->join(['c'=>'committees'], 's.committee_id=c.id', []);
                        }
                    break;

                    case 'vacant':
                        $date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);

                        $membersJoin = new Literal("s.id=m.seat_id and m.startDate <= '$date' and (m.endDate is null or '$date' <= m.endDate)");

                        $termJoin = new Literal("case when m.id is not null then m.term_id=t.id
                                          when m.id is null     then s.id=t.seat_id and t.startDate <= '$date' and (t.endDate is null or '$date' <= t.endDate)
                                      end");

                        $select->join(['c'=>'committees'], 's.committee_id=c.id', []);
                        $select->join(['m'=>'members'], $membersJoin, [], Select::JOIN_LEFT);
                        $select->join(['t'=>'terms'], $termJoin, [], Select::JOIN_LEFT);
                        $select->join(['p'=>'people'], 'm.person_id=p.id', [], Select::JOIN_LEFT);
                        $select->where("(t.startDate is not null and p.firstname is null)
                                    or  (t.startDate is null     and p.firstname is null)");
                    break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
