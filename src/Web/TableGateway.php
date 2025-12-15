<?php
/**
 * A base class that streamlines creation of ZF2 TableGateway
 *
 * @copyright 2014-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Web;

use Laminas\Db\TableGateway\TableGateway as LaminasTableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;

abstract class TableGateway
{
	protected $resultSetPrototype;
	protected $tableGateway;

	/**
	 * @param string $table The name of the database table
	 * @param string $class The class model to use as a resultSetPrototype
	 *
	 * You must pass in the fully namespaced classname.  We do not assume
	 * any particular namespace for the models.
	 */
	public function __construct($table, $class)
	{
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new $class());
		$this->tableGateway = new LaminasTableGateway(
			$table,
			Database::getConnection(),
			null,
			$this->resultSetPrototype
		);
	}

	/**
	 * Simple, default implementation for find
	 *
	 * This will allow you to do queries for rows in the table,
	 * where you provide field=>values for the where clause.
	 * Only fields actually in the table can be included this way.
	 *
	 * You generally want to override this implementation with your own
	 * However, this basic implementation will allow you to get up and
	 * running quicker.
	 *
	 * @param array   $fields    Key value pairs to select on
	 * @param string  $order     The default ordering to use for select
	 * @param int     $itemsPerPage
	 * @param int     $currentPage
	 */
	public function find(?array $fields=null, string|array|null $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
		$select = new Select($this->tableGateway->getTable());
		if ($fields) {
			foreach ($fields as $key=>$value) {
                if (isset($this->columns)) {
                    if (in_array($key, $this->columns)) {
                        $select->where([$key=>$value]);
                    }
                }
                else {
                    $select->where([$key=>$value]);
                }
			}
		}
		return $this->performSelect($select, $order, $itemsPerPage, $currentPage);
	}

	/**
	 * Laminas Paginator has been abandoned and must be removed as a dependency
	 *
	 * This function does not depend on the Laminas Paginator.  Instead it takes
	 * explicit $itemsPerPage and $currentPage, returning an array with one page
	 * of results and a total item count.
	 */
	public function performSelect(Select $select, string|array|null $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
		$total = null;
        if ($itemsPerPage) {
            $currentPage = $currentPage ? $currentPage : 1;

			$sql   = new Sql($this->tableGateway->getAdapter());
			$c     = $sql->select();
			$c->columns(['count'=>new Expression('count(*)')]);
            $c->from(['o'=>$select]);
			$q     = $sql->prepareStatementForSqlObject($c);
			$res   = $q->execute();

			$o = [];
			foreach ($res as $r) { $o[] = $r; }
			$total = $o[0]['count'];

            $select->limit ($itemsPerPage);
            $select->offset($itemsPerPage * ($currentPage-1));
        }

        if ($order) { $select->order($order); }

		$rows = [];
		$res  = $this->tableGateway->selectWith($select);
		foreach ($res as $r) { $rows[] = $r; }

        return [
            'rows'  => $rows,
            'total' => $total ?? count($rows)
        ];
	}

	/**
	 * @param  Laminas\Db\ResultSet
	 * @return array
	 */
	public static function hydrateResults(ResultSet $results)
	{
        $output = [];
        foreach ($results as $object) {
            $output[] = $object;
        }
        return $output;
	}

	/**
	 * Returns the generated sql
	 *
	 * @param Laminas\Db\Sql\Select
	 */
	public function getSqlForSelect(Select $select)
	{
		return $select->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}

	/**
	 * @param Laminas\Db\ResultSet
	 */
	public static function getSqlForResult(ResultSet $result)
	{
		return $result->getDataSource()->getResource()->queryString;
	}
}
