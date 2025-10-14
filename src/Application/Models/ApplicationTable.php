<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class ApplicationTable extends TableGateway
{
    public static $defaultOrder = ['a.archived desc', 'p.lastname', 'p.firstname'];

    public function __construct() { parent::__construct('applications', __namespace__.'\Application'); }

    public function find($fields=null, $order=null, $paginated=false, $limit=null)
    {
        if (!$order) { $order = self::$defaultOrder; }

        $select = new Select(['a'=>'applications']);
        $select->join(['p'=>'people'], 'a.person_id=p.id', []);

        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    // Datetime values are expected to be timestamps
                    case 'current':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $value);
                        $select->where("(a.archived is null or a.archived>='$date')");
                        break;

                    case 'archived':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $value);
                        $select->where("(a.archived is not null and a.archived<='$date')");
                        break;

                    case 'since':
                        $date = date(ActiveRecord::MYSQL_DATETIME_FORMAT, $value);
                        $select->where("a.created>'$date'");
                        break;

                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    public static function hasDepartment(int $department_id, int $application_id): bool
    {
        $sql    = "select a.committee_id
                   from applications a
                   join committee_departments d on a.committee_id=d.committee_id
                   where d.department_id=? and a.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $application_id]);
        return count($result) ? true : false;
    }
}
