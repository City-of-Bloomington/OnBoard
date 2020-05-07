<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\Database;
use Web\TableGateway;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Expression;

class LegislationTable extends TableGateway
{
    const TABLE = 'legislation';

    public $columns = ['id', 'number', 'year', 'type_id', 'committee_id', 'parent_id', 'status_id'];

	public function __construct() { parent::__construct('legislation', __namespace__.'\Legislation'); }

	private function processFields(array $fields=null, Select &$select)
	{
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'parent_id':
                        # parent_id may be null, and we do, in fact, want to
                        # find legislation where parent_id is null
                        $select->where([$key=>$value]);
                    break;

                    default:
                        # If there is no value, don't include the field in the search
                        if ($value) {
                            if (in_array($key, $this->columns)) {
                                $select->where([$key=>$value]);
                            }
                        }
				}
            }
        }
    }

	public function find($fields=null, $order='number desc', $paginated=false, $limit=null)
	{
		$select = new Select(self::TABLE);
		$this->processFields($fields, $select);

		return parent::performSelect($select, $order, $paginated, $limit);
	}

	public function years($fields=null)
	{
        $sql    = new Sql(Database::getConnection());
        $select = $sql->select()
                      ->from(self::TABLE)
                      ->columns([
                            'year'  => 'year',
                            'count' => new Expression('count(*)')
                        ])
                      ->group('year')
                      ->order('year desc');

        $this->processFields($fields, $select);

        $query  = $sql->prepareStatementForSqlObject($select);
        $result = $query->execute();
        $out    = [];
        foreach ($result as $row) {
            $out[$row['year']] = (int)$row['count'];
        }
        return $out;
	}

	/**
	 * Check if a legislation has a given department
     */
	public static function hasDepartment(int $department_id, int $legislation_id): bool
	{
        $sql    = "select d.department_id
                   from legislation           l
                   join committee_departments d on l.committee_id=d.committee_id
                   where d.department_id=? and l.id=?;";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$department_id, $legislation_id]);
        return count($result) ? true : false;
	}
}
