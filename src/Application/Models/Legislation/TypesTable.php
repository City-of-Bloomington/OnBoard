<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Legislation;

use Application\PdoRepository;

class TypesTable extends PdoRepository
{
    private $columns = ['id', 'name'];

	public function __construct() { parent::__construct('legislationTypes', __namespace__.'\Type'); }

	public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        $select = 'select * from legislationTypes';
        $joins  = [];
        $where  = [];
        $params = [];

		if ($fields) {
			foreach ($fields as $k=>$v) {
				switch ($k) {
                    case 'subtype':
                        $t       = $v ? 1 : 0;
                        $where[] = "subtype=$t";
                    break;

                    default:
                        if (in_array($k, $this->columns)) {
                            $where[] = "$k=:$k";
                            $params[$k] = $v;
                        }

				}
            }
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
