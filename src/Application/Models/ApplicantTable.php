<?php
/**
 * @copyright 2016-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

class ApplicantTable extends TableGateway
{
    public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

    public static $searchable_fields = ['firstname', 'lastname', 'email', 'committee_id'];

    public function search($fields=null, $order=['lastname', 'firstname'], $paginated=false, $limit=null)
    {
        $select = new Select(['p'=>'people']);
        $select->join(['a'=>'applications'], 'p.id=a.person_id', []);
        $select->group('p.id');

        if ($fields) {
            foreach ($fields as $k=>$v) {
                if ($v && in_array($k, self::$searchable_fields)) {
                    switch ($k) {
                        case 'email':
                            $select->join(['e'=>'people_emails'], 'e.person_id=p.id', []);
                            $select->where->like("e.email", "$v%");
                        break;

                        case 'committee_id':
                            $select->where(['a.committee_id'=>$v]);
                        break;

                        default:
                            $select->where->like("p.$k", "$v%");
                    }
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    /**
     * Check if the user shares a committee with the applicant
     */
    public static function shareCommittee(int $user_id, int $applicant_id): bool
    {
        $sql    = "select a.committee_id
                   from applications a
                   join members      m on a.committee_id=m.committee_id and (m.endDate is null or m.endDate > now())
                   where m.person_id=? and a.applicant_id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$user_id, $applicant_id]);
        return count($result) ? true : false;
    }

    /**
     * Check if an applicant is for a given department
     */
    public static function hasDepartment(int $department_id, int $applicant_id): bool
    {
        $sql    = "select c.department_id
                    from applications          a
                    join committee_departments c on a.committee_id=c.committee_id
                    where c.department_id=? and a.applicant_id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $applicant_id]);
        return count($result) ? true : false;
    }
}
