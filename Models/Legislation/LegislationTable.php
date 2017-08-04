<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class LegislationTable extends TableGateway
{
    public $columns = ['id', 'number', 'type_id', 'committee_id', 'parent_id'];

	public function __construct() { parent::__construct('legislation', __namespace__.'\Legislation'); }

	public function find($fields=null, $order='number desc', $paginated=false, $limit=null)
	{
        return parent::find($fields, $order, $paginated, $limit);
	}
}
