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

            $this->template->setFilename('two-column');
            $committee = $member->getCommittee();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            if ($member->getSeat_id()) {
                $this->template->blocks['contextInfo'][] = new Block('seats/summary.inc', ['seat' => $member->getSeat()]);
            }
            $this->template->blocks[] = new Block('members/list.inc', ['members'=>$member->getSeat()->getMembers()]);
            $this->template->blocks[] = new Block('members/updateForm.inc', ['member'=>$member]);
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
            try {
                if (isset($currentMember)) {
                    $currentMember->handleUpdate($_POST['currentMember']);
                    $currentMember->save();
                }

                $newMember->handleUpdate($_POST['newMember']);
                $newMember->save();

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
                    try {
                        $term = $member->getTerm();
                        if (!$member->getEndDate()) {
                            $member->setEndDate($term->getEndDate());
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

                            header('Location: '.BASE_URL.'/committees/members?committee_id='.$member->getCommittee_id());
                            exit();
                        }
                        catch (\Exception $e) {
                            $_SESSION['errorMessages'][] = $e;
                            print_r($member);
                            print_r($newMember);
                            exit();
                        }
                    }

                    $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $newMember->getCommittee()]);
                    $this->template->blocks[] = new Block('members/appointInfo.inc', [
                        'currentMember' => $member,
                        'newMember'     => $newMember
                    ]);
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
        $this->template->setFilename('two-column');
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
                    $member->setEndDate($_POST['currentMember']['endDate']);
                    $member->save();
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
                $member = new Member($_REQUEST['member_id']);
                $seat = $member->getSeat();

                $member->delete();
                header('Location: '.BASE_URL.'/seats/view?seat_id='.$seat->getId());
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        header('Location: '.BASE_URL.'/committees');
        exit();
    }
}
