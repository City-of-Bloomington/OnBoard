<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
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
	public function index()
	{
		$table = new CommitteeTable();
		$committees = $table->find();
		$this->template->blocks[] = new Block('committees/breadcrumbs.inc');
		$this->template->blocks[] = new Block('committees/list.inc', ['committees'=>$committees]);
	}

	public function view()
	{
		try {
			$committee = new Committee($_GET['committee_id']);
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			header('Location: '.BASE_URL.'/committees');
			exit();
		}

		$this->template->blocks[] = new Block('committees/panel.inc', ['committee'=>$committee]);
	}

	public function update()
	{
		if (!empty($_REQUEST['committee_id'])) {
			try {
				$committee = new Committee($_REQUEST['committee_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}
		else {
			$committee = new Committee();
		}

		if (isset($_POST['name'])) {
			try {
				$committee->handleUpdate($_POST);
				$committee->save();
				header('Location: '.$committee->getUrl());
				exit();
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		$this->template->blocks[] = new Block('committees/updateForm.inc', ['committee'=>$committee]);
	}
}
