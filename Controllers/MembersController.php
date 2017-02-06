<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
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
                $action = $member->getId() ? 'edit' : 'add';

                try {
                    $original = $member->getData();
                    $member->handleUpdate($_POST);
                    $member->save();
                    $updated  = $member->getData();

                    CommitteeHistory::saveNewEntry([
                        'committee_id'=> $member->getCommittee_id(),
                        'tablename'   => 'members',
                        'action'      => $action,
                        'changes'     => [['original'=>$original, 'updated'=>$updated]]
                    ]);

                    if ($member->getSeat_id()) {
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
                if (isset($currentMember)) {
                    $original = $currentMember->getData();
                    $currentMember->handleUpdate($_POST['currentMember']);
                    $currentMember->save();
                    $updated = $currentMember->getData();

                    $changes[] = ['original'=>$original, 'updated'=>$updated];
                }

                $original = $newMember->getData();
                $newMember->handleUpdate($_POST['newMember']);
                $newMember->save();
                $updated  = $newMember->getData();
                $changes[] = ['original'=>$original, 'updated'=>$updated];

                CommitteeHistory::saveNewEntry([
                    'committee_id'=> $newMember->getCommittee_id(),
                    'tablename'   => 'members',
                    'action'      => 'appoint',
                    'changes'     => $changes
                ]);

                header('Location: '.BASE_URL."/committees/members?committee_id={$newMember->getCommittee_id()}");
                exit();
            }
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
                    $changes = [];
                    try {
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
                    }
                    catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

                    if (empty($_SESSION['errorMessages'])
                        && !empty($_POST['confirm']) && $_POST['confirm']==='yes') {
                        try {
                            $member->save();
                            $newMember->save();
                            $changes[] = ['updated'=>$newMember->getData()];

                            CommitteeHistory::saveNewEntry([
                                'committee_id' => $newMember->getCommittee_id(),
                                'tablename'    => 'members',
                                'action'       => 'reappoint',
                                'changes'      => $changes
                            ]);

                            header('Location: '.BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id());
                            exit();
                        }
                        catch (\Exception $e) {
                            $_SESSION['errorMessages'][] = $e;
                        }
                    }

                    $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $newMember->getCommittee()]);
                    $this->template->blocks[] = new Block('members/reappointForm.inc', [
                        'member'    => $member,
                        'newMember' => $newMember
                    ]);
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
                    $original = $member->getData();
                    $member->setEndDate($_POST['currentMember']['endDate']);
                    $member->save();
                    $updated  = $member->getData();

                    CommitteeHistory::saveNewEntry([
                        'committee_id' => $member->getCommittee_id(),
                        'tablename'    => 'members',
                        'action'       => 'resign',
                        'changes'      => [['original'=>$original, 'updated'=>$updated]]
                    ]);
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

                header('Location: '.BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id());
                exit();
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
                $changes = [['original'=>$member->getData()]];

                $return_url = $member->getSeat_id()
                    ? BASE_URL."/seats/view?seat_id={$member->getSeat_id()}"
                    : BASE_URL."/committees/members?committee_id={$member->getCommittee_id()}";

                $member->delete();

                CommitteeHistory::saveNewEntry([
                    'committee_id' => $member->getCommittee_id(),
                    'tablename'    => 'members',
                    'action'       => 'delete',
                    'changes'      => $changes
                ]);

                header("Location: $return_url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        header('Location: '.BASE_URL.'/committees');
        exit();
    }
}
