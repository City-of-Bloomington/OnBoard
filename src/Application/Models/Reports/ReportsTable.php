<?php
/**
 * @copyright 2017-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Reports;

use Laminas\Db\Sql\Select;
use Web\Database;
use Web\TableGateway;

class ReportsTable extends TableGateway
{
    public $columns = ['id', 'title', 'reportDate', 'committee_id'];
	public function __construct() { parent::__construct('reports', __namespace__.'\Report'); }

	public function find(?array $fields=null, string|array|null $order=null, ?bool $paginated=false, ?int $limit=null)
    {
        $select = new Select('reports');
        if ($fields) {
            foreach ($fields as $key=>$value) {
                switch ($key) {
                    case 'indexed':
                        if ($value) {
                            $select->where(['indexed>updated']);
                        }
                        else {
                            $select->where(['indexed is null or updated>indexed']);
                        }
                        break;

                    default:
                        if (in_array($key, $this->columns)) {
                            $select->where([$key=>$value]);
                        }

                }
            }
        }
        return $this->performSelect($select, $order, $paginated, $limit);
    }

    /**
	 * Check if a report has a given department
     */
	public static function hasDepartment(int $department_id, int $report_id): bool
	{
        $sql    = "select d.department_id
                   from reports               r
                   join committee_departments d on r.committee_id=d.committee_id
                   where d.department_id=? and r.id=?;";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $report_id]);
        return count($result) ? true : false;
	}
}
