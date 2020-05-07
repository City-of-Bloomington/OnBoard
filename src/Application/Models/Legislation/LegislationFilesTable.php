<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\Database;
use Web\TableGateway;

class LegislationFilesTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationFiles', __namespace__.'\LegislationFile'); }

	/**
	 * Check if a legislation has a given department
     */
	public static function hasDepartment(int $department_id, int $file_id): bool
	{
        $sql    = "select d.department_id
                   from legislationFiles      f
                   join legislation           l on f.legislation_id=l.id
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and f.id=?;";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $file_id]);
        return count($result) ? true : false;
	}
}
