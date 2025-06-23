<?php
/**
 * @copyright 2014-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class OfficeTable extends TableGateway
{
	public function __construct() { parent::__construct('offices', __namespace__.'\Office'); }

	public function find($fields=null, $order='startDate', $paginated=false, $limit=null)
	{
		$select = new Select('offices');
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
						$date = \DateTime::createFromFormat('Y-m-d', $value);
						$select->where("startDate<='{$date->format('Y-m-d')}'");
						$select->where("(endDate is null or endDate>='{$date->format('Y-m-d')}')");
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	public static function hasDepartment(int $department_id, int $office_id): bool
    {
        $sql    = "select o.committee_id
                   from offices o
                   join committee_departments d on o.committee_id=d.committee_id
                   where d.department_id=? and o.id=?";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $office_id]);
        return count($result) ? true : false;
    }
}
