<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Application\PdoRepository;

class SeatTable extends PdoRepository
{
    public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

    public function find(array $fields=[], ?string $order='s.code, s.name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select s.* from seats s';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        if ($v) {
                            // current == true
                            $where[] = "(s.startDate is null or s.startDate<=now())";
                            $where[] = "(s.endDate   is null or s.endDate  >=now())";
                        }
                        else {
                            // current == false (the past)
                            $where[] = "(s.endDate is not null and s.endDate<=now())";
                        }
                    break;

                    case 'vacant':
                        $joins[] = 'join committees c on c.id=s.committee_id';

                        $joins[] = "left join members m
                                        on  s.id=m.seat_id
                                        and (m.startDate is null or m.startDate <= now())
                                        and (m.endDate is null or now() <= m.endDate)";

                        $joins[] = "left join terms t
                                        on case when m.id is not null then t.id=m.term_id
                                                when m.id is null     then s.id=t.seat_id
                                                                       and (t.startDate or t.startDate <= now())
                                                                       and (t.endDate is null or now() <= t.endDate)
                                        end";
                        $joins[] = 'left join people p on p.id=m.person_id';

                        $where[] = "(t.startDate is not null and p.firstname is null)
                                 or (t.startDate is null     and p.firstname is null)";
                    break;

                    default:
                        $where[] = "s.$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
     * These are the fields that will be returned for all *Data functions
     */
    public static $dataFields = [
        'committee_id'           => 's.committee_id',
        'committee_name'         => 'c.name',
        'committee_code'         => 'c.code',
        'committee_alternates'   => 'c.alternates',
        'seat_id'                => 's.id',
        'seat_code'              => 's.code',
        'seat_name'              => 's.name',
        'seat_type'              => 's.type',
        'seat_voting'            => 's.voting',
        'seat_takesApplications' => 's.takesApplications',
        'appointer_id'           => 's.appointer_id',
        'appointer_name'         => 'a.name',
        'member_id'              => 'm.id',
        'member_person_id'       => 'm.person_id',
        'member_firstname'       => 'mp.firstname',
        'member_lastname'        => 'mp.lastname',
        'member_email'           => 'me.email',
        'member_phone'           => 'mh.number',
        'member_website'         => 'mp.website',
        'member_startDate'       => 'm.startDate',
        'member_endDate'         => 'm.endDate',
        'member_termStart'       => "mt.startDate",
        'member_termEnd'         => "mt.endDate",
        'alternate_id'           => 'alt.id',
        'alternate_person_id'    => 'alt.person_id',
        'alternate_firstname'    => 'ap.firstname',
        'alternate_lastname'     => 'ap.lastname',
        'alternate_email'        => 'ae.email',
        'alternate_phone'        => 'ah.number',
        'alternate_website'      => 'ap.website',
        'alternate_startDate'    => 'alt.startDate',
        'alternate_endDate'      => 'alt.endDate',
        'alternate_termStart'    => "at.startDate",
        'alternate_termEnd'      => "at.endDate",
        'seat_startDate'         => 's.startDate',
        'seat_endDate'           => 's.endDate',
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

    private static function getDataColumns(): string
    {
        $columns = [];
        foreach (self::$dataFields as $k=>$v) {
            $columns[] = "$v as $k";
        }
        return implode(', ', $columns);
    }

    private static function bindFields(array &$where, array &$params, array $fields=[])
    {
        foreach ($fields as $k=>$v) {
            if ($k === 'current' && $v) {
                $date    = $v->format(ActiveRecord::MYSQL_DATE_FORMAT);
                $where[] = "(c.endDate is null or '$date' <= c.endDate)";
                $where[] = "((s.startDate is null or s.startDate <= '$date') and (s.endDate is null or '$date' <= s.endDate))";
            }
            if ($k === 'vacant' && $v) {
                $where[] = '(m.person_id is null or mt.id != t.id)';
            }
            elseif (array_key_exists($k, self::$dataFields)) {
                $f        = self::$dataFields[$k];
                $where[]  = "$f=:$k";
                $params[$k] = $v;
            }
        }
    }

    /**
     * Returns raw database results, instead of Model objects
     */
    private function performDataSelect(string $sql, array $params): array
    {
        $query  = $this->pdo->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'fields'  => array_keys(self::$dataFields),
            'results' => $result
        ];
    }

    public function currentData(?array $fields=null): array
    {
        if (empty( $fields['current'])) { $fields['current'] = new \DateTime(); }
        $date    = $fields['current']->format(ActiveRecord::MYSQL_DATE_FORMAT);

        $columns = self::getDataColumns();
        $select  = "select $columns from seats s";
        $joins   = [
                 'join committees   c  on  c.id =  s.committee_id',
            'left join appointers   a  on  a.id =  s.appointer_id',
            "left join terms        t  on  s.id =  t.seat_id and ((  t.startDate is null or   t.startDate <= '$date') and (  t.endDate is null or   t.endDate >= '$date'))",
            "left join members      m  on  s.id =  m.seat_id and ((  m.startDate is null or   m.startDate <= '$date') and (  m.endDate is null or   m.endDate >= '$date'))",
            "left join alternates alt  on  s.id =alt.seat_id and ((alt.startDate is null or alt.startDate <= '$date') and (alt.endDate is null or alt.endDate >= '$date'))",
            'left join terms       mt  on mt.id =  m.term_id',
            'left join terms       at  on at.id =alt.term_id',
            'left join people      mp  on mp.id =  m.person_id',
            'left join people      ap  on ap.id =alt.person_id',
            'left join people_emails me on me.person_id=mp.id and me.main=1',
            'left join people_phones mh on mh.person_id=mp.id and mh.main=1',
            'left join people_emails ae on ae.person_id=ap.id and ae.main=1',
            'left join people_phones ah on ah.person_id=ap.id and ah.main=1'
        ];
        $where  = [];
        $params = [];
        self::bindFields($where, $params, $fields);
        $sql    = parent::buildSql($select, $joins, $where, null, 'c.name,s.code');
        return $this->performDataSelect($sql, $params);
    }

    public function hasDepartment(int $department_id, int $seat_id): bool
    {
        $sql    = "select s.committee_id
                   from seats s
                   join committee_departments d on s.committee_id=d.committee_id
                   where d.department_id=? and s.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $seat_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
