<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
use Application\Models\Member;
use Application\Models\MemberTable;
use Application\Models\Seat;
use Application\Models\Term;
use Application\Models\TermTable;

use Web\Block;
use Web\Controller;
use Web\View;

class MembersController extends Controller
{
    public function update(): View
    {
        try {
            if (!empty($_REQUEST['member_id'])) { $member = new Member($_REQUEST['member_id']); }
            else {
                if     (!empty($_REQUEST['term_id'     ])) { $o = new Term($_REQUEST['term_id']); }
                elseif (!empty($_REQUEST['seat_id'     ])) { $o = new Seat($_REQUEST['seat_id']); }
                elseif (!empty($_REQUEST['committee_id'])) { $o = new Committee($_REQUEST['committee_id']); }
                $member = $o->newMember();
            }
            if (!empty($_REQUEST['person_id'])) { $member->setPerson_id($_REQUEST['person_id']); }
            if (!empty($_REQUEST['startDate'])) { $member->setStartDate($_REQUEST['startDate'], 'Y-m-d'); }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        if (isset($member)) {
            if (!empty($_POST['committee_id'])) {
                try {
                    if (!empty($_POST['endDate'])) {
                           $member->setEndDate($_POST['endDate'], 'Y-m-d');
                    }
                    else { $member->setEndDate(null); }

                    MemberTable::update($member);

                    $url = $member->getSeat_id()
                           ? View::generateUrl('seats.view').'?seat_id='.$member->getSeat_id()
                           : View::generateUrl('committees.members').'?committee_id='.$member->getCommittee_id();
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
        return $this->template;
    }

    public function appoint(): View
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

            if (isset($_REQUEST['newMember']['person_id'])) { $newMember->setPerson_id($_POST['newMember']['person_id']); }
            if (isset($_REQUEST['newMember']['startDate'])) { $newMember->setStartDate($_POST['newMember']['startDate'], 'Y-m-d'); }
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
            return $this->template;
        }

        if (isset($_POST['newMember'])) {
            try {
                $endDate = !empty($_POST['currentMember']['endDate']) ? new \DateTime($_POST['currentMember']['endDate']) : null;

                MemberTable::appoint($newMember, $endDate);

                $return_url = View::generateUrl('committees.members')."?committee_id={$newMember->getCommittee_id()}";
                header("Location: $return_url");
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
        return $this->template;
    }

    public function reappoint(): View
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
                    $return_url        = View::generateUrl('committees.members').'?committee_id='.$committee->getId();

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
        return $this->template;
    }

    public function resign(): View
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
                    $return_url = View::generateUrl('committees.members').'?committee_id='.$member->getCommittee_id();
                    header("Location: $return_url");
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
        return $this->template;
    }

    public function delete(): View
    {
        try {
            if (!empty($_REQUEST['member_id'])) {
                $member  = new Member($_REQUEST['member_id']);

                $return_url = $member->getSeat_id()
                    ? View::generateUrl('seats.view')."?seat_id={$member->getSeat_id()}"
                    : View::generateUrl('committees.members')."?committee_id={$member->getCommittee_id()}";

                MemberTable::delete($member);
                header("Location: $return_url");
                exit();
            }
        }
        catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }

        header('Location: '.View::generateUrl('committees.index'));
        exit();
        return $this->template;
    }
}
