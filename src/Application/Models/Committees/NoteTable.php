<?php
/**
 * @copyright 2025-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Committees;

use Application\PdoRepository;

class NoteTable extends PdoRepository
{
	public function __construct() { parent::__construct('committee_notes', __namespace__.'\Note'); }

    public function find(array $fields=[], ?string $order=null, ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select * from committee_notes';
        $joins  = [];
        $where  = [];
        $params = [];

		if ($fields) {
			foreach ($fields as $k=>$v) {
				switch ($k) {
					case 'archived':
						$where[] = $v ? 'archived is not null' : 'archived is null';
					break;

					case 'committee_id':
					case 'person_id':
						$where[]    = "$k=:$k";
						$params[$k] =$v;
					break;

					default:

				}
			}
		}
        $sql = self::buildSql($select, $joins, $where, null, $order);
		return $this->performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
