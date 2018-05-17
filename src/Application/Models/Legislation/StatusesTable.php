<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class StatusesTable extends TableGateway
{
    private $columns = ['id', 'name'];

	public function __construct() { parent::__construct('legislationStatuses', __namespace__.'\Status'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
        return parent::find($fields, $order, $paginated, $limit);
	}
}
