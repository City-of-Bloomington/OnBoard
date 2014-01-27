<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class AppointerTable extends TableGateway
{
	public function __construct() { parent::__construct('appointers', __namespace__.'\Appointer'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('appointers');
		if (count($fields)) {
			$this->handleJoins($select, $fields);

			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'committee_id':
						$select->where(['s.committee_id'=>$value]);
						break;

					case 'person_id':
						$select->where(['t.person_id'=>$value]);
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	private function handleJoins(Select &$select, &$fields)
	{
		foreach ($fields as $key=>$value) {
			switch ($key) {
				case 'committee_id':
					$joins['s'] = ['table'=>'seats', 'on'=>'appointers.id=s.appointer_id'];
					break;

				case 'person_id':
					$joins['s'] = ['table'=>'seats', 'on'=>'appointers.id=s.appointer_id'];
					$joins['t'] = ['table'=>'terms', 'on'=>'s.id=t.seat_id'];
					break;
			}
		}
		foreach ($joins as $alias=>$j) {
			$select->join([$alias=>$j['table']], $j['on'], []);
		}
	}
}
