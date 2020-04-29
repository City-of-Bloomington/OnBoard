<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeHistory;
use Application\Models\Seat;
use Application\Models\SeatTable;
use Application\Models\Term;

use Web\Block;
use Web\Controller;
use Web\View;

class SeatsController extends Controller
{
    /**
     * Creates a valid date from the request parameters
     *
     * In the future, we can expand this to accomodate safe parsing for
     * more parameters.
     *
     * @return array
     */
    private function parseQueryParameters()
    {
        if (!empty($_GET['current'])) {
            try {
                $c = \DateTime::createFromFormat(DATE_FORMAT, $_GET['current']);
                return ['current'=>$c];
            }
            catch (\Exception $e) { }
        }
        return [];
    }

    /**
     * Lists all the seats at a given point in time
     *
     * @param string $_GET['current'] Effective date for the query
     */
    public function index(): View
    {
        $params = $this->parseQueryParameters();
        $data   = SeatTable::currentData($params);

        if ($this->template->outputFormat === 'html') {
            $this->template->title = $this->template->_('seats_current').' - '.APPLICATION_NAME;
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
            $this->template->blocks[] = new Block('seats/header.inc');
        }
        $this->template->blocks[] = new Block('seats/data.inc', ['data'=>$data, 'params'=>$params]);
        return $this->template;
    }

    /**
     * Lists all the vacancies at a point in time
     *
     * @param string $_GET['current'] Effective date for the query
     */
    public function vacancies(): View
    {
        $params = $this->parseQueryParameters();
        $params['vacant'] = true;
        $data = SeatTable::currentData($params);

        $title = $this->template->_(['vacancy', 'vacancies', count($data['results'])]);

        if ($this->template->outputFormat === 'html') {
            $this->template->title = $title.' - '.APPLICATION_NAME;
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
            $this->template->blocks[] = new Block('seats/header.inc');
        }
        $this->template->blocks[] = new Block('seats/data.inc', ['params'=>$params, 'data'=>$data, 'title'=>$title]);
        return $this->template;
    }

    public function view(): View
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
        return $this->template;
    }

    public function update(): View
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
                    SeatTable::update($seat, $_POST);
                    header('Location: '.BASE_URL."/seats/view?seat_id={$seat->getId()}");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
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
        return $this->template;
    }

    public function delete(): View
    {
        if (!empty($_REQUEST['seat_id'])) {
            try {
                $seat = new Seat($_REQUEST['seat_id']);
                $committee_id = $seat->getCommittee_id();
                SeatTable::delete($seat);
                header('Location: '.BASE_URL."/committees/members?committee_id=$committee_id");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.BASE_URL.'/committees');
        exit();
        return $this->template;
    }

    public function end(): View
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
                    SeatTable::end($seat, $_POST);
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
        return $this->template;
    }
}
