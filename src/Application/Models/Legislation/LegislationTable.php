<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\PdoRepository;

class LegislationTable extends PdoRepository
{
    const TABLE = 'legislation';

    public $columns = ['id', 'number', 'year', 'type_id', 'committee_id', 'parent_id', 'status_id'];

	public function __construct() { parent::__construct('legislation', __namespace__.'\Legislation'); }

    private function processFields(array &$joins, array &$where, array &$params, ?array $fields=null)
    {
		if ($fields) {
			foreach ($fields as $k=>$v) {
				switch ($k) {
                    case 'parent_id':
                        # parent_id may be null, and we do, in fact, want to
                        # find legislation where parent_id is null
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                    break;

                    default:
                        # If there is no value, don't include the field in the search
                        if ($v && in_array($k, $this->columns)) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }
				}
            }
        }
    }

	public function find(array $fields=[], ?string $order='number desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        $select = 'select * from legislation';
        $joins  = [];
        $where  = [];
        $params = [];
        $this->processFields($joins, $where, $params, $fields);
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
	}

	public function search(?array $fields=null, ?string $order='number desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from legislation';
        $joins  = [];
        $where  = [];
        $params = [];
        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'id':
                    case 'year':
                    case 'committee_id':
                    case 'type_id':
                    case 'status_id':
                        if ($v) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }
                    break;
                    case 'parent_id':
                        # parent_id may be null, and we do, in fact, want to
                        # find legislation where parent_id is null
                        if ($v) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }
                        else {
                            $where[] = 'parent_id is null';
                        }
                    break;

                    default:
                        if ($v && in_array($k, $this->columns)) {
                            $where[] = "$k like :$k";
                            $params[$k] = $v;
                        }
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

	public function years(?array $fields=null): array
	{
        $select = "select year, count(*) as count from legislation";
        $joins  = [];
        $where  = [];
        $params = [];
        $group  = 'year';
        $order  = 'year desc';

        $this->processFields($joins, $where, $params, $fields);

        $sql    = parent::buildSql($select, $joins, $where, $group, $order);
        $query  = $this->pdo->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        $out    = [];
        foreach ($result as $row) {
            $out[$row['year']] = (int)$row['count'];
        }
        return $out;
	}

	/**
	 * Check if a legislation has a given department
     */
	public function hasDepartment(int $department_id, int $legislation_id): bool
	{
        $sql    = "select d.department_id
                   from legislation           l
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and l.id=?;";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $legislation_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
	}
}
