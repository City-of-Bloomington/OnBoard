<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Allocation;
use Application\Models\AllocationTable;
use Application\Models\Committee;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class AllocationsController extends Controller
{
    public function index()
    {
    }

    public function view()
    {
        if (!empty($_REQUEST['allocation_id'])) {
            try {
                $allocation = new Allocation($_REQUEST['allocation_id']);
                $this->template->blocks[] = new Block('allocations/panel.inc', ['allocation'=>$allocation]);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
    }

    public function update()
    {
        if (!empty($_REQUEST['allocation_id'])) {
            try {
                $allocation = new Allocation($_REQUEST['allocation_id']);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        elseif (!empty($_REQUEST['committee_id'])) {
            try {
                $committee  = new Committee($_REQUEST['committee_id']);
                $allocation = new Allocation();
                $allocation->setCommittee($committee);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($allocation)) {
            if (isset($_POST['allocation_id'])) {
                try {
                    $allocation->handleUpdate($_POST);
                    $allocation->save();
                    header('Location: '.BASE_URL."/committees/view?committee_id={$allocation->getCommittee_id()}");
                    exit();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
            }
            $this->template->blocks[] = new Block('committees/panel.inc', ['committee'=>$allocation->getCommittee()]);
            $this->template->blocks[] = new Block('allocations/updateForm.inc', ['allocation'=>$allocation]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
    }
}