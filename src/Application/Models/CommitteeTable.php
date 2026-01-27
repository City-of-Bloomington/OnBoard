<?php
/**
 * @copyright 2014-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;
use Web\ActiveRecord;

class CommitteeTable extends PdoRepository
{
    public function __construct() { parent::__construct('committees', __namespace__.'\Committee'); }

    public function find(?array $fields=null, ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select c.* from committees c';
        $joins  = [];
        $where  = [];
        $group  = null;
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'current':
                        // current == true|false (false is the past)
                        $where[] = $v ? '(committees.endDate is null     or  committees.endDate >= now())'
                                      : '(committees.endDate is not null and committees.endDate <= now())';
                    break;

                    case 'member_id':
                        $joins[] = 'join members m on c.id=m.committee_id';
                        $where[] = 'm.person_id=:member_id';
                        $params['member_id'] = $v;
                    break;

                    case 'liaison_id':
                        $joins[] = 'join committee_liaisons l on c.id=l.committee_id';
                        $where[] = 'l.person_id=:liaison_id';
                        $params['liaison_id'] = $v;
                    break;

                    case 'department_id':
                        $joins[] = 'committee_departments d on c.id=d.committee_id';
                        $where[] = "d.$k=:$k";
                        $params[$k] = $v;
                    break;

                    case 'legislative':
                    case  'alternates':
                        $where[] = "$k=:$k";
                        $params[$k] = $v ? 1 : 0;
                    break;

                    case 'takesApplications':
                        $joins[] = 'left join seats s on c.id=s.committee_id';
                        $group   = 'c.id';
                        $where[] = "s.$k=:$k";
                        $params[$k] = $v ? 1 : 0;
                    break;

                    default:
                        $where[] = "$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, $group, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public function end(Committee $committee, \DateTime $endDate)
    {
        $params  = [$endDate->format(ActiveRecord::MYSQL_DATE_FORMAT), $committee->getId()];
        $updates = [
            "update terms t join seats s on t.seat_id=s.id
                                 set t.endDate=? where s.committee_id=? and t.endDate is null",
            'update applications set archived=?  where committee_id=?   and archived  is null',
            'update offices      set endDate=?   where committee_id=?   and endDate   is null',
            'update seats        set endDate=?   where committee_id=?   and endDate   is null',
            'update members      set endDate=?   where committee_id=?   and endDate   is null',
            'update committees   set endDate=?   where id=?'
        ];

        $this->pdo->beginTransaction();
        try {
            foreach ($updates as $sql) {
                $q = $this->pdo->prepare($sql);
                $q->execute($params);
            }
            $this->pdo->commit();
        }
        catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function hasDepartment(int $department_id, int $committee_id): bool
    {
        $sql    = "select committee_id
                   from committee_departments
                   where department_id=? and committee_id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $committee_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
    }
}
