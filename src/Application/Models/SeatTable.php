<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Application\Database;
use Application\PdoRepository;

class SeatTable extends PdoRepository
{
    public static $valid_filters  = ['committee_id', 'appointer_id', 'status'];
    public static $valid_statuses = ['filled', 'termEndsSoon', 'carryOver', 'vacant'];

    public function __construct() { parent::__construct('seats', __namespace__.'\Seat'); }

    private static function bindFields(array &$where, array &$params, array $fields=[])
    {
        foreach ($fields as $k=>$v) {
            if (in_array($k, self::$valid_filters)) {
                $where[]    = "$k=:$k";
                $params[$k] = $v;
            }
        }
    }

    public function currentData(?array $fields=null): array
    {
        $select  = <<<END
        select * from (
        select s.committee_id      as committee_id,
               c.name              as committee_name,
               c.code              as committee_code,
               c.alternates        as committee_alternates,
               s.id                as seat_id,
               s.code              as seat_code,
               s.name              as seat_name,
               s.type              as seat_type,
               s.voting            as seat_voting,
               s.takesApplications as seat_takesApplications,
               s.appointer_id      as appointer_id,
               a.name              as appointer_name,
               m.id                as member_id,
               m.person_id         as member_person_id,
               mp.firstname        as member_firstname,
               mp.lastname         as member_lastname,
               me.email            as member_email,
               mh.number           as member_phone,
               mp.website          as member_website,
               m.startDate         as member_startDate,
               m.endDate           as member_endDate,
               mt.startDate        as member_termStart,
               mt.endDate          as member_termEnd,
               alt.id              as alternate_id,
               alt.person_id       as alternate_person_id,
               ap.firstname        as alternate_firstname,
               ap.lastname         as alternate_lastname,
               ae.email            as alternate_email,
               ah.number           as alternate_phone,
               ap.website          as alternate_website,
               alt.startDate       as alternate_startDate,
               alt.endDate         as alternate_endDate,
               at.startDate        as alternate_termStart,
               at.endDate          as alternate_termEnd,
               s.startDate         as seat_startDate,
               s.endDate           as seat_endDate,
               t.id                as term_id,
               t.startDate         as term_startDate,
               t.endDate           as term_endDate,
               case when (date_add(now(), interval c.termEndWarningDays day) > mt.endDate and now() < mt.endDate) then 'termEndsSoon'
                   when (m.person_id is not null and mt.id != t.id) then 'carryOver'
                   when m.id is null then 'vacant'
                   else 'filled'
               end as status,
               (select group_concat(concat_ws('|', o.id, o.title)) from offices o
                   where o.committee_id = s.committee_id
                       and o.person_id = m.person_id
                       and ((o.startDate is null or o.startDate <= now())
                       and  (o.endDate   is null or o.endDate   >= now()))) as offices,
               (select endDate from members where seat_id = s.id order by endDate desc limit 1) as last_member_endDate
        from      seats s
            join committees     c on  c.id =   s.committee_id
        left join appointers     a on  a.id =   s.appointer_id
        left join terms          t on  s.id =   t.seat_id and ((  t.startDate is null or   t.startDate <= now()) and (  t.endDate is null or   t.endDate >= now()))
        left join members        m on  s.id =   m.seat_id and ((  m.startDate is null or   m.startDate <= now()) and (  m.endDate is null or   m.endDate >= now()))
        left join alternates   alt on  s.id = alt.seat_id and ((alt.startDate is null or alt.startDate <= now()) and (alt.endDate is null or alt.endDate >= now()))
        left join terms         mt on mt.id =   m.term_id
        left join terms         at on at.id = alt.term_id
        left join people        mp on mp.id =   m.person_id
        left join people        ap on ap.id = alt.person_id
        left join people_emails me on mp.id =  me.person_id and me.main=1
        left join people_phones mh on mp.id =  mh.person_id and mh.main=1
        left join people_emails ae on ap.id =  ae.person_id and ae.main=1
        left join people_phones ah on ap.id =  ah.person_id and ah.main=1
        where (c.endDate is null or now() <= c.endDate)
          and ((s.startDate is null or s.startDate <= now()) and (s.endDate is null or now() <= s.endDate))
        ) x
        END;

        $where  = [];
        $params = [];
        self::bindFields($where, $params, $fields);
        $sql    = parent::buildSql($select, [], $where, null, 'committee_name, seat_name');
        return Database::query($sql, $params);
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
