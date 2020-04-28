<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
use Application\Models\Member;
use Application\Models\MemberTable;
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
                    MemberTable::update($_POST, $member);

                    $url = $member->getSeat_id()
                         ? BASE_URL.'/seats/view?seat_id='.$member->getSeat_id()
                         : BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id();
                    header("Location: $url");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            if (isset($_REQUEST['person_id'])) {
                try { $member->setPerson_id($_REQUEST['person_id']); }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $this->template->setFilename('contextInfo');
            $committee = $member->getCommittee();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('members/updateForm.inc', ['member'=>$member]);
            $seat = $member->getSeat();
            if ($seat) {
                $this->template->blocks['contextInfo'][] = new Block('seats/summary.inc', ['seat' => $seat]);
                $this->template->blocks[] = new Block('members/list.inc', [
                    'members'        => $seat->getMembers(),
                    'disableButtons' => true
                ]);
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function appoint()
    {
        try {
            if (    !empty($_REQUEST['term_id'     ])) { $o = new Term     ($_REQUEST['term_id']); }
            elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat     ($_REQUEST['seat_id']); }
            elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
            elseif (!empty($_REQUEST['newMember']['term_id'     ])) { $o = new Term     ($_REQUEST['newMember']['term_id']); }
            elseif (!empty($_REQUEST['newMember']['seat_id'     ])) { $o = new Seat     ($_REQUEST['newMember']['seat_id']); }
            elseif (!empty($_REQUEST['newMember']['committee_id'])) { $o = new Committee($_REQUEST['newMember']['committee_id']); }
            $newMember = $o->newMember();

            $seat = $newMember->getSeat();
            if ($seat) {
                // If the current member has already been closed out,
                // there's no reason to include them in the form
                $currentMember = $seat->getLatestMember();
                if ($currentMember && $currentMember->getEndDate()) { unset($currentMember); }
            }
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return;
        }

        if (isset($_POST['newMember'])) {
            $changes = [];
            try {
                MemberTable::appoint($_POST, $newMember, isset($currentMember) ? $currentMember : null);

                header('Location: '.BASE_URL."/committees/members?committee_id={$newMember->getCommittee_id()}");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($_REQUEST['newMember']['person_id'])) {
            try { $newMember->setPerson_id($_REQUEST['newMember']['person_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $form = new Block('members/appointForm.inc', ['newMember' => $newMember]);

        $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $newMember->getCommittee()]);
        if (isset($currentMember)) {
            $form->currentMember = $currentMember;
        }
        $this->template->blocks[] = $form;
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
                    $confirmationGiven = !empty($_POST['confirm']) && $_POST['confirm']=='yes';
                    $committee         = $member->getCommittee();
                    $return_url        = BASE_URL.'/committees/members?committee_id='.$committee->getId();

                    try { $vars = MemberTable::reappoint($_POST, $member, $confirmationGiven); }
                    catch (\Exception $e) {
                        $_SESSION['errorMessages'][] = $e;
                        header('Location: '.$return_url);
                        exit();
                    }

                    if (empty($_SESSION['errorMessages']) && $confirmationGiven) {
                        header('Location: '.$return_url);
                        exit();
                    }

                    $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
                    $this->template->blocks[] = new Block('members/reappointForm.inc', $vars);
                }
            }

        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function resign()
    {
        $this->template->setFilename('contextInfo');
        try {
            if (              !empty($_REQUEST['member_id'])) {
                $member = new Member($_REQUEST['member_id']);
            }
            elseif (          !empty($_REQUEST['currentMember']['member_id'])) {
                $member = new Member($_REQUEST['currentMember']['member_id']);
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        if (isset($member)) {
            if (!empty($_POST['currentMember'])) {
                try {
                    MemberTable::resign($_POST, $member);
                    header('Location: '.BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

            }

            $seat = $member->getSeat();
            if ($seat) {
                $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $member->getCommittee()]);
                $this->template->blocks['contextInfo'][] = new Block('seats/summary.inc', ['seat' => $seat]);
            }
            $this->template->blocks[] = new Block('members/resignForm.inc', ['currentMember'=>$member]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        try {
            if (!empty($_REQUEST['member_id'])) {
                $member  = new Member($_REQUEST['member_id']);

                $return_url = $member->getSeat_id()
                    ? BASE_URL."/seats/view?seat_id={$member->getSeat_id()}"
                    : BASE_URL."/committees/members?committee_id={$member->getCommittee_id()}";

                MemberTable::delete($member);
                header("Location: $return_url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        header('Location: '.BASE_URL.'/committees');
        exit();
    }
}
