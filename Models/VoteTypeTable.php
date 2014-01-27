<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class VoteTypeTable extends TableGateway
{
	public function __construct() { parent::__construct('voteTypes', __namespace__.'\VoteType'); }

	public function find($fields=null, $order='ordering', $paginated=false, $limit=null)
	{
		return parent::find($fields, $order, $paginated, $limit);
	}
}
