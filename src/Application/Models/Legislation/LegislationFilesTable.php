<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\PdoRepository;

class LegislationFilesTable extends PdoRepository
{
	public function __construct() { parent::__construct('legislationFiles', __namespace__.'\LegislationFile'); }

	public function find(array $fields=[], ?string $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from legislationFiles';
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
                        $where[]    = "$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
	 * Check if a legislation has a given department
     */
	public function hasDepartment(int $department_id, int $file_id): bool
	{
        $sql    = "select d.department_id
                   from legislationFiles      f
                   join legislation           l on f.legislation_id=l.id
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and f.id=?;";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $file_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
	}
}
