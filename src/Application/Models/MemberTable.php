<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class MemberTable extends PdoRepository
{
    public function __construct() { parent::__construct('members', __namespace__.'\Member'); }

    public function find(?array $fields=null, ?string $order='startDate desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from members';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        if (is_object($v) && get_class($v)=='DateTime') {
                            $d       = $v->format('Y-m-d');
                            $where[] = "startDate <= '$d'";
                            $where[] = "(endDate is null or endDate >= '$d')";
                        }
                        elseif ($v) {
                            // current == true (the present)
                            $where[] = "startDate <= now()";
                            $where[] = "(endDate is null or endDate >= now())";
                        }
                        else {
                            // current == false (the past)
                            $where[] = "(endDate is not null and endDate <= now())";
                        }
                        break;

                    default:
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function isMember(int $person_id, int $committee_id): bool
    {
        $sql    = "select id from members where person_id=? and committee_id=? and (endDate is null or endDate > now())";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$person_id, $committee_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }

    public function hasDepartment(int $department_id, int $member_id): bool
    {
        $sql    = "select m.committee_id
                   from members m
                   join committee_departments d on m.committee_id=d.committee_id
                   where d.department_id=? and m.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $member_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
