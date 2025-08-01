<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class ApplicantTable extends TableGateway
{
    public function __construct() { parent::__construct('applicants', __namespace__.'\Applicant'); }

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
