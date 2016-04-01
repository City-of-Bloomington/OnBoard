<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
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
        $data = SeatTable::currentData();

        if ($this->template->outputFormat === 'html') {
            $this->template->title = $this->template->_('seats_current').' - '.APPLICATION_NAME;
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
            $this->template->blocks[] = new Block('seats/header.inc');
        }
        $this->template->blocks[] = new Block('seats/data.inc', ['data'=>$data]);
    }

    public function vacancies()
    {
        $data  = SeatTable::currentData(['vacant'=>true]);
        $title = $this->template->_(['vacancy', 'vacancies', count($data['results'])]);

        if ($this->template->outputFormat === 'html') {
            $this->template->title = $title.' - '.APPLICATION_NAME;
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
            $this->template->blocks[] = new Block('seats/header.inc');
        }
        $this->template->blocks[] = new Block('seats/data.inc', ['data'  => $data, 'title' => $title]);
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
                header('Location: '.BASE_URL."/seats/view?seat_id={$seat->getId()}");
                exit();
            }
            $committee = $seat->getCommittee();
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee'=>$committee]);
            $this->template->blocks[] = new Block('seats/updateForm.inc', ['seat'=>$seat]);
            $this->template->blocks[] = new block('seats/list.inc', [
                'seats'     => $committee->getSeats(),
                'committee' => $committee,
                'disableButtons' => true
            ]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }

    public function delete()
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
                $committee_id = $seat->getCommittee_id();

                $seat->delete();
                header('Location: '.BASE_URL."/committees/members?committee_id=$committee_id");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.BASE_URL.'/committees');
        exit();
    }

    public function end()
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['endDate'])) {
                try {
                    $seat->saveEndDate($_POST['endDate']);
                    header('Location: '.BASE_URL.'/seats/view?seat_id='.$seat->getId());
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('seats/endDateForm.inc', ['seat'=>$seat]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}
