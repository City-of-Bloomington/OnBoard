<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\Seat;
use Application\Models\SeatTable;
use Application\Models\Term;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class SeatsController extends Controller
{
    public function index()
    {
    }

    public function view()
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee'=>$seat->getCommittee()]);
            $this->template->blocks[] = new Block('seats/info.inc', ['seat'=>$seat]);
            if ($seat->getType() === 'termed') {
                $this->template->blocks[] = new Block('terms/list.inc', [
                    'terms' => $seat->getTerms(),
                    'seat'  => $seat
                ]);
            }
            else {
                $this->template->blocks[] = new Block('members/list.inc', [
                    'members'   => $seat->getMembers(),
                    'seat'      => $seat,
                    'committee' => $seat->getCommittee()
                ]);
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['committee_id'])) {
            try {
                $committee = new Committee($_REQUEST['committee_id']);
                $seat = new Seat();
                $seat->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['committee_id'])) {
                try {
                    $seat->handleUpdate($_POST);
                    $seat->save();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                header('Location: '.BASE_URL."/committees/seats?committee_id={$seat->getCommittee_id()}");
                exit();
            }
            #$this->template->blocks[] = new Block('committees/panel.inc', ['committee'=>$seat->getCommittee()]);
            $this->template->blocks[] = new Block('seats/updateForm.inc', ['seat'=>$seat]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function appoint()
    {
        try {
            if (          !empty($_REQUEST['term_id'])) {
                $term = new Term($_REQUEST['term_id']);
                $seat = $term->getSeat();
                $newMember = $term->newMember();
            }
            elseif (      !empty($_REQUEST['seat_id'])) {
                $seat = new Seat($_REQUEST['seat_id']);
                $newMember = $seat->newMember();
            }
            elseif (      !empty($_REQUEST['newMember']['term_id'])) {
                $term = new Term($_REQUEST['newMember']['term_id']);
                $seat = $term->getSeat();
                $newMember = $term->newMember();
            }
            elseif (      !empty($_REQUEST['newMember']['seat_id'])) {
                $seat = new Seat($_REQUEST['newMember']['seat_id']);
                $newMember = $seat->newMember();
            }

            // If the current member has already been closed out,
            // there's no reason to include them in the form
            $currentMember = $seat->getLatestMember();
            if ($currentMember && $currentMember->getEndDate()) { unset($currentMember); }
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

                header('Location: '.BASE_URL."/committees/members?committee_id={$seat->getCommittee_id()}");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        $form = new Block('seats/appointForm.inc', ['newMember' => $newMember]);
        if (isset($currentMember)) { $form->currentMember = $currentMember; }

        $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $seat->getCommittee()]);
        $this->template->blocks[] = new Block('seats/summary.inc', ['seat' => $seat]);
        if (isset($currentMember)) {
            $this->template->blocks[] = new Block('members/list.inc', [
                'members' => [$currentMember],
                'title'   => $this->template->_('previous_member'),
                'disableButtons' => true
            ]);
        }
        $this->template->blocks[] = $form;
    }
}
