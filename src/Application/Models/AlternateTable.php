<?php
/**
 * @copyright 2022-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class AlternateTable extends PdoRepository
{
    public function __construct() { parent::__construct('alternates', __namespace__.'\Alternate'); }

    public function find(array $fields=[], ?string $order='startDate desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from alternates';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        if ($v) {
                            $where[] = 'startDate <= now()';
                            $where[] = '(endDate is null or endDate >= now())';
                        }
                        else {
                            // current == false (the past)
                            $where[] = '(endDate is not null and endDate <= now())';
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

    //----------------------------------------------------------------
    // Route Action Functions
    //
    // These are functions that match the actions defined in the route
    //----------------------------------------------------------------
    public static function update(Alternate $alternate)
    {
        if ($alternate->getId()) {
            $action   = 'edit';
            $original = new Alternate($alternate->getId());
        }
        else {
            $action   = 'add';
            $original = [];
        }

        $alternate->save();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $alternate->getCommittee_id(),
            'tablename'   => 'alternates',
            'action'      => $action,
            'changes'     => [['original'=>$original, 'updated'=>$alternate->getData()]]
        ]);
    }

    public static function delete(Alternate $alternate)
    {
        $committee_id = $alternate->getCommittee_id();
        $changes      = [['original'=>$alternate->getData()]];
        $alternate->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $committee_id,
            'tablename'    => 'alternates',
            'action'       => 'delete',
            'changes'      => $changes
        ]);
    }

    public function hasDepartment(int $department_id, int $alternate_id): bool
    {
        $sql    = "select a.committee_id
                   from alternates a
                   join committee_departments d on a.committee_id=d.committee_id
                   where d.department_id=? and a.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $alternate_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
