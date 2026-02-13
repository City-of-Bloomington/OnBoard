<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class OfficeTable extends PdoRepository
{
    public function __construct() { parent::__construct('offices', __namespace__.'\Office'); }

    public function find(array $fields=[], ?string $order='startDate', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from offices';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        $date    = \DateTime::createFromFormat('Y-m-d', $v);
                        $where[] = "startDate<='{$date->format('Y-m-d')}'";
                        $where[] = "(endDate is null or endDate>='{$date->format('Y-m-d')}')";
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

    public function hasDepartment(int $department_id, int $office_id): bool
    {
        $sql    = "select o.committee_id
                   from offices o
                   join committee_departments d on o.committee_id=d.committee_id
                   where d.department_id=? and o.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $office_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
