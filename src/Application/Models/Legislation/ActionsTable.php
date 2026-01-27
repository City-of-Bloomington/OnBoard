<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\PdoRepository;

class ActionsTable extends PdoRepository
{
	public function __construct() { parent::__construct('legislationActions', __namespace__.'\Action'); }

	public function find(array $fields=[], ?string $order='actionDate', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
	}

	/**
	 * Check if a legislation has a given department
     */
	public function hasDepartment(int $department_id, int $action_id): bool
	{
        $sql    = "select d.department_id
                   from legislationActions    a
                   join legislation           l on a.legislation_id=l.id
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and a.id=?";
        $query  = $this->pdo->prepare($sql);
        $query->execute([$department_id, $action_id]);
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return count($result) ? true : false;
	}
}
