<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Web\TableGateway;

class ActionTypesTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationActionTypes', __namespace__.'\ActionType'); }

	public function find(?array $fields=null, string|array|null $order='ordering', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
	}
}
