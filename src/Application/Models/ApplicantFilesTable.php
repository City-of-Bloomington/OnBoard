<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class ApplicantFilesTable extends TableGateway
{
    const TABLE = 'applicantFiles';

    public function __construct() { parent::__construct(self::TABLE, __namespace__.'\ApplicantFile'); }

    public function find($fields=null, $order='updated desc', $paginated=false, $limit=null)
    {
        $select = new Select(self::TABLE);
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    default:
                        $select->where([$key=>$value]);
                }
            }
        }
        return parent::performSelect($select, $order, $paginated, $limit);
    }

    /**
     * Check if the user shares a committee with the file's applicant.
     */
    public static function shareCommittee(int $user_id, int $file_id): bool
    {
        $sql    = "select a.committee_id
                   from applicantFiles f
                   join applications   a on f.applicant_id=a.applicant_id
                   join members        m on a.committee_id=m.committee_id and (m.endDate is null or m.endDate > now())
                   where m.person_id=? and  f.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$user_id, $file_id]);
        return count($result) ? true : false;
    }

    /**
     * Check if an applicant is for a given department
     */
    public static function hasDepartment(int $department_id, int $file_id): bool
    {
        $sql    = "select c.department_id
                    from applicantFiles f
                    join applications   a on f.applicant_id=a.applicant_id
                    join committee_departments c on a.committee_id=c.committee_id
                    where c.department_id=? and f.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $file_id]);
        return count($result) ? true : false;
    }
}
