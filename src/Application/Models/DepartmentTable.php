<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class DepartmentTable extends PdoRepository
{
    public function __construct() { parent::__construct('departments', __namespace__.'\Department'); }

    public function find(?array $fields=null, ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select d.* from departments d';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'committee_id':
                        $joins[] = 'join committee_departments c on d.id=c.department_id';
                        $where[] = "c.$k=:$k";
                        $params[$k] = $v;
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
}
