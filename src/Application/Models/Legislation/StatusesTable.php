<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Application\PdoRepository;

class StatusesTable extends PdoRepository
{
    private $columns = ['id', 'name', 'active'];

	public function __construct() { parent::__construct('legislationStatuses', __namespace__.'\Status'); }

	public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
	{
        return parent::find($fields, $order, $itemsPerPage, $currentPage);
	}
}
