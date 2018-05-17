<?php
/**
 * @copyright 2014-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class CommitteeTable extends TableGateway
{
	public function __construct() { parent::__construct('committees', __namespace__.'\Committee'); }

	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('committees');
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'current':
                        // current == true|false (false is the past)
                        $value
                            ? $select->where("(committees.endDate is null     or  committees.endDate >= now())")
                            : $select->where("(committees.endDate is not null and committees.endDate <= now())");
                    break;

					case 'member_id':
						$select->join(['m'=>'members'], 'committees.id=m.committee_id', []);
						$select->where(['m.person_id' => $value]);
					break;

					case 'liaison_id':
                        $select->join(['l'=>'committee_liaisons'], 'committees.id=l.committee_id', []);
                        $select->where(['l.person_id' => $value]);
					break;

					case 'legislative':
                        $select->where(['legislative'=>$value ? 1 : 0]);
					break;

					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}

	//----------------------------------------------------------------
	// Route Action Functions
	//
	// These are functions that match the actions defined in the route
	//----------------------------------------------------------------
	public static function update(Committee $committee, array $post)
	{
        $action = $committee->getId() ? 'edit' : 'add';
        $change = $action == 'edit' ? [CommitteeHistory::STATE_ORIGINAL=>$committee->getData()] : [];

        $committee->handleUpdate($post);
        $committee->save();
        $change[CommitteeHistory::STATE_UPDATED] = $committee->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $committee->getId(),
            'tablename'   => 'committees',
            'action'      => $action,
            'changes'     => [$change]
        ]);
	}

	public static function end(Committee $committee, array $post)
	{
        $change = [CommitteeHistory::STATE_ORIGINAL => $committee->getData()];
        $committee->saveEndDate($post['endDate']);
        $change[CommitteeHistory::STATE_UPDATED] = $committee->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $committee->getId(),
            'tablename'   => 'committees',
            'action'      => 'end',
            'changes'     => [$change]
        ]);
	}
}
