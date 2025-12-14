<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Web\TableGateway;
use Laminas\Db\Sql\Select;

class StatusesTable extends TableGateway
{
    private $columns = ['id', 'name', 'active'];

	public function __construct() { parent::__construct('legislationStatuses', __namespace__.'\Status'); }

	public function find(?array $fields=null, string|array|null $order='name', ?bool $paginated=false, ?int $limit=null)
	{
        return parent::find($fields, $order, $paginated, $limit);
	}
}
