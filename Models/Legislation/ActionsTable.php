<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;

class ActionsTable extends TableGateway
{
	public function __construct() { parent::__construct('legislationActions', __namespace__.'\Action'); }

	public function find($fields=null, $order='actionDate', $paginated=false, $limit=null)
	{
        return parent::find($fields, $order, $paginated, $limit);
	}
}
