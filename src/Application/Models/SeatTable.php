<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Literal;

class SeatTable extends TableGateway
{
	public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

	public function find($fields=null, $order=['s.code', 's.name'], $paginated=false, $limit=null)
	{
		$select = new Select(['s'=>'seats']);
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
                        if ($value) {
                            // current == true
                            $select->where("(s.startDate is null or s.startDate<=now())");
                            $select->where("(s.endDate   is null or s.endDate  >=now())");
                        }
                        else {
                            // current == false (the past)
                            $select->where("(s.endDate is not null and s.endDate<=now())");
                        }

                    break;

                    case 'vacant':
                        $membersJoin = new Literal("s.id=m.seat_id and (m.startDate is null or m.startDate <= now()) and (m.endDate is null or now() <= m.endDate)");

                        $termJoin = new Literal("case when m.id is not null then m.term_id=t.id
                                                      when m.id is null     then s.id=t.seat_id and (t.startDate or t.startDate <= now()) and (t.endDate is null or now() <= t.endDate)
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

    /**
     * These are the fields that will be returned for all *Data functions
     */
    public static $dataFields = [
        'committee_id'           => 's.committee_id',
        'committee_name'         => 'c.name',
        'seat_id'                => 's.id',
        'seat_code'              => 's.code',
        'seat_name'              => 's.name',
        'seat_type'              => 's.type',
        'seat_voting'            => 's.voting',
        'seat_takesApplications' => 's.takesApplications',
        'appointer_name'         => 'a.name',
        'member_id'              => 'm.id',
        'person_id'              => 'm.person_id',
        'person_firstname'       => 'p.firstname',
        'person_lastname'        => 'p.lastname',
        'person_email'           => 'p.email',
        'person_website'         => 'p.website',
        'person_address'         => 'p.address',
        'person_city'            => 'p.city',
        'person_state'           => 'p.state',
        'person_zip'             => 'p.zip',
        'seat_startDate'         => 's.startDate',
        'seat_endDate'           => 's.endDate',
        'member_startDate'       => 'm.startDate',
        'member_endDate'         => 'm.endDate',
        'member_termStart'       => "mt.startDate",
        'member_termEnd'         => "mt.endDate",
        'term_id'                => 't.id',
        'term_startDate'         => "t.startDate",
        'term_endDate'           => "t.endDate",
        // Calculated fields
        'termEndsSoon'    => '(date_add(now(), interval c.termEndWarningDays day) > mt.endDate and now() < mt.endDate)',
        'carryOver'       => '(m.person_id is not null and mt.id != t.id)',
        'offices'         => "( select group_concat(concat_ws('|',o.id,o.title))
                                from offices o
                                where o.committee_id=s.committee_id
                                and o.person_id=m.person_id
                                and ((o.startDate is null or o.startDate <= now()) and (o.endDate is null or o.endDate >= now())))"
    ];

    /**
     * Return SQL for columns used in the SELECT
     *
     * @return string
     */
    private static function getDataColumns()
    {
        $columns = [];
        foreach (self::$dataFields as $k=>$v) {
            $columns[] = "$v as $k";
        }
        return implode(', ', $columns);
    }

    /**
     * Prepares sql for the WHERE and binds values for all values
     *
     * @param array $fields
     * @return array [$where, $params]
     */
    private static function bindFields($fields=null)
    {
        $where  = [];
        $params = [];
        if (count($fields)) {
            foreach ($fields as $k=>$v) {
                if ($k === 'current' && $v) {
                    $date    = $v->format(ActiveRecord::MYSQL_DATE_FORMAT);
                    $where[] = "((s.startDate is null or s.startDate <= '$date') and (s.endDate is null or '$date' <= s.endDate))";
                }
                if ($k === 'vacant' && $v) {
                    $where[] = '(m.person_id is null or mt.id != t.id)';
                }
                elseif (array_key_exists($k, self::$dataFields)) {
                    $f        = self::$dataFields[$k];
                    $where[]  = "$f=?";
                    $params[] = $v;
                }
            }
            $where = 'where '.implode(' and ', $where);
        }
        else {
            $where  = '';
            $params = null;
        }
        return [$where, $params];
    }

    /**
     * @param string $sql
     * @param array $params
     */
    private static function performDataSelect($sql, $params)
    {
        $db = Database::getConnection();
        $result = $db->query($sql)->execute($params);
        return [
            'fields'  => array_keys(self::$dataFields),
            'results' => $result
        ];
    }

    /**
     * @param array $fields
     * @return array
     */
    public static function currentData(array $fields=null)
    {
        if (empty($fields['current'])) { $fields['current'] = new \DateTime(); }
        $date   = $fields['current']->format(ActiveRecord::MYSQL_DATE_FORMAT);

        list($where, $params) = self::bindFields($fields);

        $columns = self::getDataColumns();
        $sql = "select  $columns
                from seats           s
                join committees      c  on s.committee_id=c.id
                left join appointers a  on s.appointer_id=a.id
                left join members    m  on s.id=m.seat_id and ((m.startDate is null or m.startDate <= '$date') and (m.endDate is null or m.endDate >= '$date'))
                left join terms      mt on m.term_id=mt.id
                left join terms      t  on s.id=t.seat_id and ((t.startDate is null or t.startDate <= '$date') and (t.endDate is null or t.endDate >= '$date'))
                left join people     p on m.person_id=p.id
                $where
                order by c.name, s.code";
        return self::performDataSelect($sql, $params);
	}

	//----------------------------------------------------------------
	// Route Action Functions
	//
	// These are functions that match the actions defined in the route
	//----------------------------------------------------------------
	public static function update(Seat $seat)
	{
        if ($seat->getId()) {
            $action   = 'edit';
            $original = new Seat($seat->getId());
        }
        else {
            $action   = 'add';
            $original = new Seat();
        }
        $changes = [CommitteeHistory::STATE_ORIGINAL => $original->getData(),
                    CommitteeHistory::STATE_UPDATED  =>     $seat->getData()];

        $seat->save();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $seat->getCommittee_id(),
            'tablename'    => 'seats',
            'action'       => $action,
            'changes'      => $changes
        ]);
	}

	public static function delete(Seat $seat)
	{
        $committee_id = $seat->getCommittee_id();
        $change = [CommitteeHistory::STATE_ORIGINAL=>$seat->getData()];
        $seat->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $committee_id,
            'tablename'    => 'seats',
            'action'       => 'delete',
            'changes'      => [$change]
        ]);
	}

	public static function end(Seat $seat, \DateTime $endDate)
	{
        $change[CommitteeHistory::STATE_ORIGINAL] = $seat->getData();
        $seat->saveEndDate($endDate);
        $change[CommitteeHistory::STATE_UPDATED ] = $seat->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id' =>$seat->getCommittee_id(),
            'tablename'    =>'seats',
            'action'       =>'end',
            'changes'      =>[$change]
        ]);
	}
}
