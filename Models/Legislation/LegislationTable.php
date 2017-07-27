<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;

class LegislationTable extends TableGateway
{
	public function __construct() { parent::__construct('legislation', __namespace__.'\Legislation'); }

	public function find($fields=null, $order='number desc', $paginated=false, $limit=null)
	{
        return parent::find($fields, $order, $paginated, $limit);
	}
}
