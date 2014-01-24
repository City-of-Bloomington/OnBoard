<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Seat;
use Application\Models\SeatTable;
use Application\Models\Requirement;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class SeatsController extends Controller
{
	/**
	 * @param int $id
	 * @return Seat
	 */
	private function loadSeat($id)
	{
		try {
			$seat = new Seat($id);
			return $seat;
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/commitees');
			exit();
		}
	}
	public function index()
	{
	}

	public function update()
	{
		if (empty($_REQUEST['seat_id']) && !empty($_REQUEST['committee_id'])) {
			try {
				$seat = new Seat();
				$seat->setCommittee_id($_REQUEST['committee_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/commitees');
				exit();
			}
		}
		else {
			$seat = $this->loadSeat($_REQUEST['seat_id']);
		}

		if (isset($_POST['name'])) {
			try {
				$seat->handleUpdate($_POST);
				$seat->save();
				header('Location: '.$seat->getUrl());
				exit();
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$seat->getCommittee()]);
		$this->template->blocks[] = new Block('seats/updateForm.inc', ['seat'=>$seat]);
	}

	public function updateRequirements()
	{
		$seat = $this->loadSeat($_REQUEST['seat_id']);

		try {
			if (isset($_POST['text'])) {
				$text = trim($_POST['text']);
				if ($text) {
					$requirement = new Requirement();
					$requirement->setText($text);
					$requirement->save();
				}
				elseif (!empty($_POST['requirement_id'])) {
					$requirement = new Requirement($_POST['requirement_id']);
				}
			}
			if (isset($requirement)) {
				$seat->addRequirement($requirement);
			}
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$seat->getCommittee()]);
		$this->template->blocks[] = new Block('seats/info.inc', ['seat'=>$seat]);
		$this->template->blocks[] = new Block('seats/requirementsForm.inc', ['seat'=>$seat]);
	}

	public function removeRequirement()
	{
		$seat = $this->loadSeat($_REQUEST['seat_id']);
		
		$requirement = new Requirement($_REQUEST['requirement_id']);

		$seat->removeRequirement($requirement);

		header('Location: '.BASE_URL.'/seats/updateRequirements?seat_id='.$seat->getId());
		exit();
	}
}
