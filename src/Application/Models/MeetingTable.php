<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class MeetingTable extends PdoRepository
{
    public static $sortableFields = ['start'];

    public function __construct() { parent::__construct('meetings', __namespace__.'\Meeting'); }

    private function processFields(array &$joins, array &$where, array &$params, array $fields=[])
    {
        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'start':
                        $where[] = 'm.start >= :start';
                        $params['start'] = $v->format('Y-m-d H:i:s');
                        break;

                    case 'end':
                        $where[] = 'm.end <= :end';
                        $params['end'] = $v->format('Y-m-d H:i:s');
                        break;

                    case 'year':
                        $where[] = 'year(m.start)=:year';
                        $params['year'] = (int)$v;
                        break;

                    case 'fileType':
                        $joins[] = 'join meetingFiles f on m.id=f.meeting_id';
                        $where[] = 'f.type=:type';
                        $params['type'] = $v;
                        break;

                    default:
                        $where[] = "m.$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
    }

    public function find(array $fields=[], ?string $order='start', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select m.* from meetings m';
        $joins  = [];
        $where  = [];
        $params = [];

        $this->processFields($joins, $where, $params, $fields);
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }


    public function years(?array $fields=null): array
    {
        $select = "select distinct(year(m.start)) as year, count(*) as count from meetings as m";
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

    public function hasDepartment(int $department_id, int $meeting_id): bool
    {
        $sql    = "select m.committee_id
                   from meetings m
                   join committee_departments d on m.committee_id=d.committee_id
                   where d.department_id=? and m.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $meeting_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
