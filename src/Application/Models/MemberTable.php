<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\TableGateway;
use Laminas\Db\Sql\Select;

class MemberTable extends TableGateway
{
	public function __construct() { parent::__construct('members', __namespace__.'\Member'); }

	public function find($fields=null, $order='startDate desc', $paginated=false, $limit=null)
	{
		$select = new Select('members');
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'current':
                        if ($value) {
                            $select->where("startDate <= now()");
                            $select->where("(endDate is null or endDate >= now())");
                        }
                        else {
                            // current == false (the past)
                            $select->where("(endDate is not null and endDate <= now())");
                        }
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
	public static function update(array $post, Member $member)
	{
        $action   = $member->getId() ? 'edit' : 'add';
        $original = $member->getData();
        $member->handleUpdate($post);
        $member->save();

        $updated  = $member->getData();
        CommitteeHistory::saveNewEntry([
            'committee_id'=> $member->getCommittee_id(),
            'tablename'   => 'members',
            'action'      => $action,
            'changes'     => $action == 'edit'
                           ? [['original'=>$original, 'updated'=>$updated]]
                           : [['updated'=>$updated]]
        ]);
	}

	public static function appoint(array $post, Member $newMember, Member $currentMember=null)
	{
        if ($currentMember) {
            $original = $currentMember->getData();
            $currentMember->handleUpdate($post['currentMember']);
            $currentMember->save();
            $updated = $currentMember->getData();

            $changes[] = ['original'=>$original, 'updated'=>$updated];
        }

        $original = $newMember->getData();
        $newMember->handleUpdate($post['newMember']);
        $newMember->save();
        $updated  = $newMember->getData();
        $changes[] = ['original'=>$original, 'updated'=>$updated];

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $newMember->getCommittee_id(),
            'tablename'   => 'members',
            'action'      => 'appoint',
            'changes'     => $changes
        ]);
	}

	public static function reappoint(array $post, Member $member, bool $confirm=false)
	{
        $changes = [];
        $term = $member->getTerm();
        if (!$member->getEndDate()) {
            $original = $member->getData();
            $member->setEndDate($term->getEndDate());
            $updated  = $member->getData();
            $changes[] = ['original'=>$original, 'updated'=>$updated];
        }

        $next      = $term->getNextTerm();
        $newMember = $next->newMember();
        $newMember->setPerson_id($member->getPerson_id());
        $newMember->setStartDate($next->getStartDate());

        if ($confirm) {
            $member->save();
            $newMember->save();
            $changes[] = ['updated'=>$newMember->getData()];

            CommitteeHistory::saveNewEntry([
                'committee_id' => $newMember->getCommittee_id(),
                'tablename'    => 'members',
                'action'       => 'reappoint',
                'changes'      => $changes
            ]);
        }

        return [
            'member'    => $member,
            'newMember' => $newMember
        ];
	}

	public static function resign(array $post, Member $member)
	{
        $original = $member->getData();
        $member->setEndDate($post['currentMember']['endDate']);
        $member->save();
        $updated  = $member->getData();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $member->getCommittee_id(),
            'tablename'    => 'members',
            'action'       => 'resign',
            'changes'      => [['original'=>$original, 'updated'=>$updated]]
        ]);
	}

	public static function delete(Member $member)
	{
        $committee_id = $member->getCommittee_id();
        $changes      = [['original'=>$member->getData()]];
        $member->delete();

        CommitteeHistory::saveNewEntry([
            'committee_id' => $committee_id,
            'tablename'    => 'members',
            'action'       => 'delete',
            'changes'      => $changes
        ]);
	}
}
