<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Office;
use Application\Models\OfficeTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class OfficesController extends Controller
{
	public function index()
	{
	}

	public function update()
	{
		if (empty($_REQUEST['office_id'])) {
			$office = new Office();

			if (!empty($_REQUEST['committee_id']) && !empty($_REQUEST['person_id'])) {
				$office->setCommittee_id($_REQUEST['committee_id']);
				$office->setPerson_id($_REQUEST['person_id']);
			}
			else {
				$_SESSION['errorMessages'][] = new \Exception('offices/missingCommittee');
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}
		else {
			try {
				$office = new Office($_REQUEST['office_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.BASE_URL.'/committees');
				exit();
			}
		}

		if (isset($_POST['title'])) {
			try {
				$office->handleUpdate($_POST);
				$office->save();
				header('Location: '.$office->getCommittee()->getUrl());
				exit();
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		$this->template->blocks[] = new Block('committees/info.inc', ['committee'=>$office->getCommittee()]);
		$this->template->blocks[] = new Block('offices/updateForm.inc', ['office'=>$office]);
	}
}
