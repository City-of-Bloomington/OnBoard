<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Members\Appoint;

use Application\Models\ApplicationTable;
use Application\Models\CommitteeHistory;
use Application\Models\Member;
use Application\Models\MemberTable;
use Application\Models\Term;
use Application\Models\Seat;
use Application\Models\Committee;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            if (    !empty($_REQUEST['term_id'     ])) { $o = new Term     ($_REQUEST['term_id']); }
            elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat     ($_REQUEST['seat_id']); }
            elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }

            if (isset($o)) { $member = $o->newMember(); }
            else { throw new \Exception('missingRequiredFields'); }
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
            return new \Web\Views\NotFoundView();
        }

        parent::captureNewReturnUrl(\Web\View::generateUrl('committees.members', ['committee_id'=>$member->getCommittee_id()]));

        if (isset($_POST['committee_id'])) {
            $member->setPerson_id($_POST['person_id']);
            $member->setStartDate($_POST['startDate']);
            $member->setEndDate(!empty($_POST['endDate']) ? $_POST['endDate'] : null);


            $table = new ApplicationTable();
            $apps  = $table->find(['current'     => time(),
                                  'committee_id' => $member->getCommittee_id(),
                                     'person_id' => $member->getPerson_id()]);

            try {
                $member->save();
                $change = ['original'=>[], 'updated'=>$member->getData()];

                CommitteeHistory::saveNewEntry([
                    'committee_id'=> $member->getCommittee_id(),
                    'tablename'   => 'members',
                    'action'      => 'appoint',
                    'changes'     => [$change]
                ]);
                foreach ($apps['rows'] as $a) { $a->archive(); }

                $return_url = parent::popCurrentReturnUrl();
                unset($_SESSION['return_url']);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($member, $_SESSION['return_url']);
    }
}
