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
            $this->template->blocks[] = new Block('seats/panel.inc', ['seat'=>$seat]);
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
        if (!empty($_REQUEST['term_id'])) {
            try {
                $term = new Term($_REQUEST['term_id']);
                $seat = $term->getSeat();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['seat_id'])) {
                try {
                    $seat->handleAppointment($_POST);
                    header('Location: '.BASE_URL."/committees/members?committee_id={$seat->getCommittee_id()}");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }

            $form = new Block('seats/appointForm.inc', ['seat' => $seat]);
            if (isset($term)) { $form->term = $term; }

            $this->template->blocks[] = new Block('seats/panel.inc', ['seat' => $seat]);
            $this->template->blocks[] = $form;
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
