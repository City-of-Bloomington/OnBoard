<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Committee;
use Application\Models\CommitteeTable;
use Application\Models\Seat;
use Application\Models\VoteTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

class CommitteesController extends Controller
{
    private function loadCommittee($id)
    {
        try {
            return new Committee($id);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
            header('Location: '.BASE_URL.'/committees');
            exit();
        }
    }

    public function index()
    {
        $data = Committee::data();
        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc');
        }
        $this->template->blocks[] = new Block('committees/list.inc', ['data'=>$data]);
    }

    public function info()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }
        $this->template->blocks[] = new Block('committees/info.inc', ['committee' => $committee]);
        $this->template->blocks[] = new Block('departments/list.inc', [
            'departments'    => $committee->getDepartments(),
            'disableButtons' => true
        ]);
        $this->template->blocks[] = new Block('committees/liaisons.inc', ['committee' => $committee]);
    }

    public function members()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }
        if ($committee->getType() === 'seated') {
            $seats = $committee->getSeats(time());
            $this->template->blocks[] = new Block('committees/partials/seatedMembers.inc', [
                'committee' => $committee,
                'seats'     => $seats,
                'title'     => $this->template->_(['current_member', 'current_members', count($seats)])
            ]);
        }
        else {
            $members = $committee->getMembers();
            $this->template->blocks[] = new Block('committees/partials/openMembers.inc', [
                'committee' => $committee,
                'members'   => $members,
                'title'     => $this->template->_(['current_member', 'current_members', count($members)])
            ]);
        }

    }

    public function update()
    {
        $committee =        !empty($_REQUEST['committee_id'])
            ? $this->loadCommittee($_REQUEST['committee_id'])
            : new Committee();

        if (isset($_POST['name'])) {
            try {
                $committee->handleUpdate($_POST);
                $committee->save();

                $url = BASE_URL."/committees/info?committee_id={$committee->getId()}";
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }
        $this->template->blocks[] = new Block('committees/updateForm.inc',  ['committee' => $committee]);
    }

    public function seats()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);
        if ($this->template->outputFormat === 'html') {
            $this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
            $this->template->blocks[] = new Block('committees/header.inc',      ['committee' => $committee]);
        }
        $this->template->blocks[] = new block('seats/list.inc', [
            'seats'     => $committee->getSeats(),
            'committee' => $committee
        ]);
    }

    public function applications()
    {
        $committee = $this->loadCommittee($_GET['committee_id']);

        $this->template->blocks[] = new Block('committees/breadcrumbs.inc',  ['committee' => $committee]);
        $this->template->blocks[] = new Block('committees/header.inc',       ['committee' => $committee]);
        $this->template->blocks[] = new Block('applications/reportForm.inc', ['committee' => $committee]);
        $this->template->blocks[] = new Block('applications/list.inc', [
            'committee'    => $committee,
            'applications' => $committee->getApplications(['archived'=>time()]),
            'title'        => $this->template->_('applications_archived'),
            'type'         => 'archived'
        ]);

    }
}
