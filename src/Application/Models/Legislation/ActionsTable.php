<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\Database;
use Web\TableGateway;

class ActionsTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationActions', __namespace__.'\Action'); }

	public function find(?array $fields=null, string|array|null $order='actionDate', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
	}

	/**
	 * Check if a legislation has a given department
     */
	public static function hasDepartment(int $department_id, int $action_id): bool
	{
        $sql    = "select d.department_id
                   from legislationActions    a
                   join legislation           l on a.legislation_id=l.id
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and a.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $action_id]);
        return count($result) ? true : false;
	}
}
