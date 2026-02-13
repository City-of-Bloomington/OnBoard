<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Legislation;

use Application\PdoRepository;

class ActionTypesTable extends PdoRepository
{
	public function __construct() { parent::__construct('legislationActionTypes', __namespace__.'\ActionType'); }

	public function find(array $fields=[], ?string $order='ordering', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
	}
}
