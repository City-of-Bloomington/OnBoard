<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application;

use Web\Database;

abstract class PdoRepository
{
    protected string $table;
    protected string $class;
    protected \PDO   $pdo;

    public function __construct(string $table, string $class)
    {
        $db          = Database::getConnection();
        $this->pdo   = $db->getDriver()->getConnection()->getResource();
        $this->table = $table;
        $this->class = $class;
    }

    public function find(array $fields=[], ?string $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from '.$this->table;
        $joins  = [];
        $where  = [];
        $params = [];

		if ($fields) {
			foreach ($fields as $k=>$v) {
                if (isset($this->columns)) {
                    if (in_array($k, $this->columns)) {
                        $where[]    = "$k=:$k";
                        $params[$k] = $v;
                    }
                }
                else {
                    $where[]    = "$k=:$k";
                    $params[$k] = $v;
                }
			}
		}
        $sql = self::buildSql($select, $joins, $where, null, $order);
		return $this->performSelect($sql, $params, $itemsPerPage, $currentPage);
    }

    public static function buildSql(string $select, array $joins, array $where, ?string $group=null, ?string $order=null): string
    {
        $sql = $select;
        if ($joins) { $sql.=' '.implode(' ', $joins); }
        if ($where) { $sql.=' where '.implode(' and ', $where); }
        if ($group) { $sql.=" group by $group"; }
        if ($order) { $sql.=" order by $order"; }
        return $sql;
    }

	protected function performSelect(string $select, array $params, int $itemsPerPage=null, ?int $currentPage=null): array
    {
		$total = null;
        if ($itemsPerPage) {
            $currentPage = $currentPage ? $currentPage : 1;

            $sql    = "select count(*) as count from ($select) o";
            $qq     = $this->pdo->prepare($sql);
            $qq->execute($params);
            $r      = $qq->fetchAll(\PDO::FETCH_ASSOC);
            $total  = $r[0]['count'];

            $offset = $itemsPerPage * ($currentPage-1);
            $select.= " limit $itemsPerPage offset $offset";
        }

		$rows  = [];
        $qq    = $this->pdo->prepare($select);
        $qq->execute($params);
        $res   = $qq->fetchAll(\PDO::FETCH_ASSOC);
        $model = $this->class;
		foreach ($res as $r) { $rows[] = new $model($r); }

        return [
            'rows'  => $rows,
            'total' => $total ?? count($rows)
        ];
    }
}
