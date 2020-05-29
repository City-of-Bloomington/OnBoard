<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\TableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Predicate\Like;

class PeopleTable extends TableGateway
{
    public static $columns = ['firstname', 'lastname'];

	public function __construct() { parent::__construct('people', __namespace__.'\Person'); }

	public function find($fields=null, $order='lastname', $paginated=false, $limit=null)
	{
		$select = new Select('people');
		$select->quantifier(Select::QUANTIFIER_DISTINCT);

		if ($fields) {
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

					case 'liaison':
                        if ($value) {
                            $select->join(['l'=>'committee_liaisons'], 'people.id=l.person_id', []);
                        }
					break;

					case 'committee_id':
						$select->join(['t'=>'terms'], 'people.id=t.person_id', []);
						$select->join(['s'=>'seats'], 't.seat_id=s.id',        []);
						$select->where(['s.committee_id'=>$value]);
                    break;

                    case 'email':
                    case 'phone':
                        if (Person::isAllowed('people', 'viewContactInfo')) {
                            $select->where([$key=>$value]);
                        }
                    break;

                    case 'department_id':
                        if ($value) { $select->where([$key=>$value]); }
                    break;

					default:
                        if (in_array($key, self::$columns)) {
                            $select->where([$key=>$value]);
                        }
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	public function search($fields, $order='lastname', $paginated=false, $limit=null)
	{
		$select = new Select('people');
		foreach ($fields as $k => $v) {
            switch ($k) {
                case 'user_account':
                    if ($v) { $select->where('username is not null'); }
                    else    { $select->where('username is null'    ); }
                break;

                case 'role':
                case 'department_id':
                case 'authenticationMethod':
                    if ($v) { $select->where([$k=>$v]); }
                break;

                default:
                    if ($v && in_array($k, self::$columns)) {
                        $select->where->like($k, "$v%");
                    }
            }
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
