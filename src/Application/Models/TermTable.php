<?php
/**
 * @copyright 2014-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class TermTable extends TableGateway
{
    public function __construct() { parent::__construct('terms', __namespace__.'\Term'); }

    public function find(?array $fields=null, string|array|null $order='startDate desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = new Select('terms');
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'current':
                        $date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
                        $select->where("(startDate is null or startDate<='$date')");
                        $select->where("(endDate   is null or endDate  >='$date')");
                        break;

                    case 'before':
                        $date = date(ActiveRecord::MYSQL_DATE_FORMAT, $value);
                        $select->where("startDate is null or startDate < '$date'");
                        $select->where("endDate < '$date'");
                        break;

                    case 'committee_id':
                        $select->join(['s'=>'seats'], 'terms.seat_id=s.id', [] );
                        $select->where(['s.committee_id'=>$value]);
                        break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $itemsPerPage, $currentPage);
    }

    //----------------------------------------------------------------
    // Route Action Functions
    //
    // These are functions that match the actions defined in the route
    //----------------------------------------------------------------
    public static function update(Term $term)
    {
        if ($term->getId()) {
            $action   = 'edit';
            $original = new Term($term->getId());
        }
        else {
            $action   = 'add';
            $original = [];
        }
        $change  = [CommitteeHistory::STATE_ORIGINAL => $original,
                    CommitteeHistory::STATE_UPDATED  => $term->getData()];

        $term->save();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $term->getSeat()->getCommittee_id(),
            'tablename'    => 'terms',
            'action'       => $action,
            'changes'      => [$change]
        ]);
    }

    public static function delete(Term $term)
    {
        $seat   = $term->getSeat();
        $change = [CommitteeHistory::STATE_ORIGINAL=>$term];
        $term->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $seat->getCommittee_id(),
            'tablename'    => 'terms',
            'action'       => 'delete',
            'changes'      => [$change]
        ]);
    }

    public static function hasDepartment(int $department_id, int $term_id): bool
    {
        $sql    = "select s.committee_id
                   from terms                 t
                   join seats                 s on t.seat_id=s.id
                   join committee_departments d on s.committee_id=d.committee_id
                   where d.department_id=? and t.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $term_id]);
        return count($result) ? true : false;
    }
}
