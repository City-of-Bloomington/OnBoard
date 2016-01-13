<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Member;
use Application\Models\Seat;
use Application\Models\Term;
use Application\Models\TermTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class MembersController extends Controller
{
    public function index() { }

    public function update()
    {
        try {
            if (isset($_REQUEST['member_id'])) { $member = new Member($_REQUEST['member_id']); }
            else {
                if     (isset($_REQUEST['term_id'     ])) { $o = new Term($_REQUEST['term_id']); }
                elseif (isset($_REQUEST['seat_id'     ])) { $o = new Seat($_REQUEST['seat_id']); }
                elseif (isset($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
                $member = $o->newMember();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        if (isset($member)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $member->handleUpdate($_POST);
                    $member->save();

                    if ($member->getTerm_id()) {
                        $url = BASE_URL.'/terms/view?term_id='.$member->getTerm_id();
                    }
                    elseif ($member->getSeat_id()) {
                        $url = BASE_URL.'/seats/view?seat_id='.$member->getSeat_id();
                    }
                    else {
                        $url = BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id();
                    }
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->blocks[] = $member->getSeat_id()
                ? new Block('seats/panel.inc',      ['seat'      => $member->getSeat()])
                : new Block('committees/panel.inc', ['committee' => $member->getCommittee()]);

            if ($member->getTerm_id()) {
                $this->template->blocks[] = new Block('members/list.inc', ['members'=>$member->getTerm()->getMembers()]);
            }

            $this->template->blocks[] = new Block('members/updateForm.inc', ['member'=>$member]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function reappoint()
    {
        if (!empty($_REQUEST['member_id'])) {
            try {
                $member = new Member($_REQUEST['member_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($member)) {
            $seat = $member->getSeat();
            if ($seat) {
                if ($seat->getType() === 'termed') {
                    try {
                        $term = $member->getTerm();
                        if (!$member->getEndDate()) {
                            $member->setEndDate($term->getEndDate());
                            $member->save();
                        }

                        $next      = $term->getNextTerm();
                        $newMember = $next->newMember();
                        $newMember->setPerson_id($member->getPerson_id());
                        $newMember->setStartDate($next->getStartDate());
                        $newMember->save();
                    }
                    catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                }
            }

            header('Location: '.BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id());
            exit();
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}