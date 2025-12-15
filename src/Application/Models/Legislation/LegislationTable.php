<?php
/**
 * @copyright 2017-2025 City of Bloomington, Indiana
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

	private function processFields(Select &$select, ?array $fields=null)
	{
		if ($fields) {
			foreach ($fields as $k=>$v) {
				switch ($k) {
                    case 'parent_id':
                        # parent_id may be null, and we do, in fact, want to
                        # find legislation where parent_id is null
                        $select->where([$k=>$v]);
                    break;

                    default:
                        # If there is no value, don't include the field in the search
                        if ($v && in_array($k, $this->columns)) {
                            $select->where([$k=>$v]);
                        }
				}
            }
        }
    }

	public function find(?array $fields=null, string|array|null $order='number desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
		$select = new Select(self::TABLE);
		$this->processFields($select, $fields);

		return parent::performSelect($select, $order, $itemsPerPage, $currentPage);
	}

	public function search(?array $fields=null, string|array|null $order='number desc', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
		$select = new Select(self::TABLE);
        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'id':
                    case 'year':
                    case 'type_id':
                    case 'status_id':
                        if ($v) { $select->where([$k=>$v]); }
                    break;
                    case 'parent_id':
                        # parent_id may be null, and we do, in fact, want to
                        # find legislation where parent_id is null
                        $select->where([$k=>$v]);
                    break;

                    default:
                        if ($v && in_array($k, $this->columns)) {
                            $select->where->like($k, "$v%");
                        }
                }
            }
        }
        return parent::performSelect($select, $order, $itemsPerPage, $currentPage);
    }

	public function years(?array $fields=null): array
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

        $this->processFields($select, $fields);

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
