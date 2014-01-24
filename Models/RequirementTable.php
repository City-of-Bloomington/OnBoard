<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class RequirementTable extends TableGateway
{
	public function __construct() { parent::__construct('requirements', __namespace__.'\Requirement'); }

	public function find($fields=null, $order='id', $paginated=false, $limit=null)
	{
		$select = new Select('requirements');

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'seat_id':
						$select->join(['sr'=>'seat_requirements'], 'requirements.id=sr.requirement_id', []);
						$select->where(['sr.seat_id'=>$value]);
						break;
					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
