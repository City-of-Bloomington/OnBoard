<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Allocation;
use Application\Models\Seat;
use Application\Models\SeatTable;
use Application\Models\Requirement;
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
        elseif (!empty($_REQUEST['allocation_id'])) {
            try {
                $allocation = new Allocation($_REQUEST['allocation_id']);
                $seat = new Seat();
                $seat->setAllocation($allocation);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($seat)) {
            if (isset($_POST['allocation_id'])) {
                try {
                    $seat->handleUpdate($_POST);
                    $seat->save();
                }
                catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
                header('Location: '.BASE_URL."/allocations/view?allocation_id={$seat->getAllocation_id()}");
                exit();
            }
            $this->template->blocks[] = new Block('allocations/panel.inc', ['allocation'=>$seat->getAllocation()]);
            $this->template->blocks[] = new Block('seats/updateForm.inc',  ['seat'=>$seat]);
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }

	}
}
