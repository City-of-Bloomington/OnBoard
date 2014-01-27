<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Like;

class PeopleTable extends TableGateway
{
	public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

	public function find($fields=null, $order='lastname', $paginated=false, $limit=null)
	{
		$select = new Select('people');
		$select->quantifier(Select::QUANTIFIER_DISTINCT);

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'user_account':
						if ($value) {
							$select->where('username is not null');
						}
						else {
							$select->where('username is null');
						}
					break;

					case 'committee_id':
						$select->join(['t'=>'terms'], 'people.id=t.person_id', []);
						$select->join(['s'=>'seats'], 't.seat_id=s.id',        []);
						$select->where(['s.committee_id'=>$value]);
						break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	public function search($fields, $order='lastname', $paginated=false, $limit=null)
	{
		$select = new Select('people');

		$searchableFields = ['firstname', 'lastname', 'email'];
		foreach ($searchableFields as $f) {
			if (isset($fields[$f])) {
				$value = trim($fields[$f]);
				if ($value) {
					$select->where->like($f, "$value%");
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
