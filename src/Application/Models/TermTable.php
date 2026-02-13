<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Application\PdoRepository;

class TermTable extends PdoRepository
{
    public function __construct() { parent::__construct('terms', __namespace__.'\Term'); }

    public function find(array $fields=[], ?string $order='startDate desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select t.* from terms t';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        $date    = date(ActiveRecord::MYSQL_DATE_FORMAT, $v);
                        $where[] = "(t.startDate is null or t.startDate<='$date')";
                        $where[] = "(t.endDate   is null or t.endDate  >='$date')";
                        break;

                    case 'before':
                        $date    = date(ActiveRecord::MYSQL_DATE_FORMAT, $v);
                        $where[] = "(t.startDate is null or t.startDate < '$date')";
                        $where[] =  "t.endDate < '$date'";
                        break;

                    case 'committee_id':
                        $joins[] = 'join seats s on s.id=t.seat_id';
                        $where[] = 's.committee_id=:committee_id';
                        $params['committee_id'] = $v;
                        break;

                    default:
                        $where[] = "t.$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function hasDepartment(int $department_id, int $term_id): bool
    {
        $sql    = "select s.committee_id
                   from terms                 t
                   join seats                 s on t.seat_id=s.id
                   join committee_departments d on s.committee_id=d.committee_id
                   where d.department_id=? and t.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $term_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
