<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Literal;

class SeatTable extends TableGateway
{
	public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

	public function find($fields=null, $order=['s.code', 's.name'], $paginated=false, $limit=null)
	{
		$select = new Select(['s'=>'seats']);
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
                        // Both the committee and the seat must be current
                        $select->join(['c'=>'committees'], 's.committee_id=c.id', []);

                        if ($value) {
                            // current == true
                            $select->where("(c.endDate   is null or c.endDate>=now())");
                            $select->where("(s.startDate is null or s.startDate<=now())");
                            $select->where("(s.endDate   is null or s.endDate>=now())");
                        }
                        else {
                            // current == false (the past)
                            $select->where("(c.endDate   is not null and c.endDate<=now())");
                            $select->where("(s.endDate   is not null and s.endDate<=now())");
                        }

                    break;

                    case 'vacant':
                        $membersJoin = new Literal("s.id=m.seat_id and m.startDate <= now() and (m.endDate is null or now() <= m.endDate)");

                        $termJoin = new Literal("case when m.id is not null then m.term_id=t.id
                                                      when m.id is null     then s.id=t.seat_id and t.startDate <= now() and (t.endDate is null or now() <= t.endDate)
                                                end");

                        $select->join(['c'=>'committees'], 's.committee_id=c.id', []);
                        $select->join(['m'=>'members'   ], $membersJoin,          [], Select::JOIN_LEFT);
                        $select->join(['t'=>'terms'     ], $termJoin,             [], Select::JOIN_LEFT);
                        $select->join(['p'=>'people'    ], 'm.person_id=p.id',    [], Select::JOIN_LEFT);
                        $select->where("(t.startDate is not null and p.firstname is null)
                                    or  (t.startDate is null     and p.firstname is null)");
                    break;

					default:
						$select->where(["s.$key" => $value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
