<?php
/**
 * @copyright 2016-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
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
	public static function update(Member $member)
	{
        if ($member->getId()) {
            $action   = 'edit';
            $original = new Member($member->getId());
        }
        else {
            $action   = 'add';
            $original = [];
        }

        $member->save();

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $member->getCommittee_id(),
            'tablename'   => 'members',
            'action'      => $action,
            'changes'     => [['original'=>$original, 'updated'=>$member->getData()]]
        ]);
	}

	public static function appoint(Member $newMember, ?\DateTime $currentMemberEndDate=null)
	{
        $seat = $newMember->getSeat();
        if ($seat) {
            // Close out the current member
            $currentMember = $seat->getLatestMember();
            if ($currentMember && !$currentMember->getEndDate() && $currentMemberEndDate) {
                $original  = $currentMember->getData();
                $currentMember->setEndDate($currentMemberEndDate->format('Y-m-d'), 'Y-m-d');
                $currentMember->save();
                $updated   = $currentMember->getData();
                $changes[] = ['original'=>$original, 'updated'=>$updated];
            }
        }

        $newMember->save();
        $changes[] = ['original'=>[], 'updated'=>$newMember->getData()];

        CommitteeHistory::saveNewEntry([
            'committee_id'=> $newMember->getCommittee_id(),
            'tablename'   => 'members',
            'action'      => 'appoint',
            'changes'     => $changes
        ]);
	}

	/**
     * Returns the new membership on success
     */
	public static function reappoint(Member $member): Member
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

        $member->save();
        $newMember->save();
        $changes[] = ['updated'=>$newMember->getData()];

        CommitteeHistory::saveNewEntry([
            'committee_id' => $newMember->getCommittee_id(),
            'tablename'    => 'members',
            'action'       => 'reappoint',
            'changes'      => $changes
        ]);

        return $newMember;
	}

	public static function resign(Member $member, \DateTime $endDate)
	{
        $original = $member->getData();
        $member->setEndDate($endDate->format('Y-m-d'));
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

	public static function isMember(int $person_id, int $committee_id): bool
	{
        $sql    = "select id from members where person_id=? and committee_id=? and (endDate is null or endDate > now())";
        $db     = Database::getConnection();
        $result = $db->query($sql)->execute([$person_id, $committee_id]);
        return count($result) ? true : false;
	}
}
