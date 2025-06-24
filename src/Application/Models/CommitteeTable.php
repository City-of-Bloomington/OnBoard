<?php
/**
 * @copyright 2014-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class CommitteeTable extends TableGateway
{
    public function __construct() { parent::__construct('committees', __namespace__.'\Committee'); }

    public function find($fields=null, $order='name', $paginated=false, $limit=null)
    {
        $select = new Select('committees');
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'current':
                        // current == true|false (false is the past)
                        $value
                            ? $select->where("(committees.endDate is null     or  committees.endDate >= now())")
                            : $select->where("(committees.endDate is not null and committees.endDate <= now())");
                    break;

                    case 'member_id':
                        $select->join(['m'=>'members'], 'committees.id=m.committee_id', []);
                        $select->where(['m.person_id' => $value]);
                    break;

                    case 'liaison_id':
                        $select->join(['l'=>'committee_liaisons'], 'committees.id=l.committee_id', []);
                        $select->where(['l.person_id' => $value]);
                    break;

                    case 'department_id':
                        $select->join(['d'=>'committee_departments'], 'committees.id=d.committee_id', []);
                        $select->where(['d.department_id' => $value]);
                    break;

                    case 'legislative':
                    case  'alternates':
                        $select->where([$key=>$value ? 1 : 0]);
                    break;

                    case 'takesApplications':
                        $select->join(['s'=>'seats'], 'committees.id=s.committee_id', [], Select::JOIN_LEFT);
                        $select->group('committees.id');
                        $select->where(['s.takesApplications' => (bool)$value]);
                    break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    //----------------------------------------------------------------
    // Route Action Functions
    //
    // These are functions that match the actions defined in the route
    //----------------------------------------------------------------
    /**
     * @return int   committee_id
     */
    public static function update(Committee $committee, array $post): int
    {
        $action = $committee->getId() ? 'edit' : 'add';
        $change = $action == 'edit' ? [CommitteeHistory::STATE_ORIGINAL=>$committee->getData()] : [];

        $committee->handleUpdate($post);
        $committee->save();
        $change[CommitteeHistory::STATE_UPDATED] = $committee->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $committee->getId(),
            'tablename'   => 'committees',
            'action'      => $action,
            'changes'     => [$change]
        ]);

        return (int)$committee->getId();
    }

    public static function end(Committee $committee, \DateTime $endDate)
    {
        $db      = Database::getConnection();
        $change  = [CommitteeHistory::STATE_ORIGINAL => $committee->getData()];
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

        $db->getDriver()->getConnection()->beginTransaction();
        try {
            foreach ($updates as $sql) { $db->query($sql)->execute($params); }
            $db->getDriver()->getConnection()->commit();
        }
        catch (\Exception $e) {
            $db->getDriver()->getConnection()->rollback();
            throw $e;
        }

        $change[CommitteeHistory::STATE_UPDATED] = $committee->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $committee->getId(),
            'tablename'   => 'committees',
            'action'      => 'end',
            'changes'     => [$change]
        ]);
    }

    public static function hasDepartment(int $department_id, int $committee_id): bool
    {
        $sql    = "select committee_id
                   from committee_departments
                   where department_id=? and committee_id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $committee_id]);
        return count($result) ? true : false;
    }
}
