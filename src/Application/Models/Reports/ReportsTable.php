<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Reports;

use Application\PdoRepository;

class ReportsTable extends PdoRepository
{
    public $columns = ['id', 'title', 'reportDate', 'committee_id'];
	public function __construct() { parent::__construct('reports', __namespace__.'\Report'); }

	public function find(array $fields=[], ?string $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from reports';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'indexed':
                        $where[] = $v
                                 ? 'indexed>updated'
                                 : 'indexed is null or updated>indexed';
                        break;

                    default:
                        if (in_array($k, $this->columns)) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }

                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
	 * Check if a report has a given department
     */
	public function hasDepartment(int $department_id, int $report_id): bool
	{
        $sql    = "select d.department_id
                   from reports               r
                   join committee_departments d on r.committee_id=d.committee_id
                   where d.department_id=? and r.id=?;";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $report_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
	}
}
