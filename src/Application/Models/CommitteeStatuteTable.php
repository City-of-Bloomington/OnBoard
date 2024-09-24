<?php
/**
 * @copyright 2016-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class CommitteeStatuteTable extends TableGateway
{
	public function __construct() { parent::__construct('committeeStatutes', __namespace__.'\CommitteeStatute'); }

	/**
     * Check if a statute has a given department
     */
    public static function hasDepartment(int $department_id, int $statute_id): bool
    {
        $sql = "select d.department_id
                from committeeStatutes     s
                join committee_departments d on s.committee_id=d.committee_id
                where d.department_id=? and s.id=?;";
        $db  = Database::getConnection();
        $res = $db->query($sql)->execute([$department_id, $statute_id]);
        return count($res) ? true : false;
    }
}
