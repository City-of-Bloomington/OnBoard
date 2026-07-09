<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class CommitteeFilesTable extends PdoRepository
{
    const TABLE = 'committeeFiles';
    public static $sortableFields = ['filename', 'start', 'created'];

    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\CommitteeFile'); }

    private function processFields(array &$joins, array &$where, array &$params, ?array $fields=null)
    {
        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'start':
                        $where[] = 'm.start >= :start';
                        $params['start'] = $v->format('Y-m-d');
                    break;

                    case 'end':
                        $where[] = 'm.start <= :end';
                        $params['end'] = $v->foramt('Y-m-d');
                    break;

                    case 'year':
                        $where[] = 'year(m.start)=:year';
                        $params['year'] = (int)$v;
                    break;

                    case 'indexed':
                        $where[] = $v
                                 ? 'f.indexed>f.updated'
                                 : 'f.indexed is null or f.updated>f.indexed';
                    break;

                    default:
                        if ($v) { $where[] = "$k=:$k"; $params[$k] = $v; }
                        else    { $where[] = "$k is null"; }
                }
            }
        }
    }

    public function find(?array $fields=null, string|array|null $order='f.updated desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select f.* from committeeFiles f';
        $joins  = ['join committees c on c.id=f.committee_id'];
        $where  = [];
        $params = [];

        $this->processFields($joins, $where, $params, $fields);
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    /**
     * Check if a file has a given department
     * @see Web\Auth\DepartmentAssociation
     */
    public function hasDepartment(int $department_id, int $file_id): bool
    {
        $sql    = "select d.department_id
                   from committeeFiles        f
                   join committee_departments d on f.committee_id=d.committee_id
                   where d.department_id=? and f.id=?;";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $file_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
