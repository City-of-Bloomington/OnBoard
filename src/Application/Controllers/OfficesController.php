<?php
/**
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Controllers;

use Application\Models\Office;
use Application\Models\OfficeTable;

use Web\Block;
use Web\Controller;
use Web\View;

class OfficesController extends Controller
{
	public function update(): View
	{
		if (empty($_REQUEST['office_id'])) {
			$office = new Office();

			if (!empty($_REQUEST['committee_id']) && !empty($_REQUEST['person_id'])) {
				$office->setCommittee_id($_REQUEST['committee_id']);
				$office->setPerson_id($_REQUEST['person_id']);
			}
			else {
				$_SESSION['errorMessages'][] = new \Exception('offices/missingCommittee');
				header('Location: '.View::generateUrl('committees.index'));
				exit();
			}
		}
		else {
			try {
				$office = new Office($_REQUEST['office_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header('Location: '.View::generateUrl('committees.index'));
				exit();
			}
		}

		if (isset($_POST['title'])) {
			try {
				$office->setTitle($_POST['title']);
				$office->setStartDate($_POST['startDate'], 'Y-m-d');
				if (!empty($_POST['endDate'])) {
                    $office->setEndDate($_POST['endDate'], 'Y-m-d');
				}
				else {
                    $office->setEndDate(null);
				}

				$office->save();
				$return_url = View::generateUrl('committees.members').'?committee_id='.$office->getCommittee_id();
				header("Location: $return_url");
				exit();
			}
			catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
		}

		$committee = $office->getCommittee();
		$this->template->blocks[] = new Block('committees/breadcrumbs.inc', ['committee' => $committee]);
		$this->template->blocks[] = new Block('offices/list.inc',           ['offices'   => $committee->getOffices(date('Y-m-d'))]);
		$this->template->blocks[] = new Block('offices/updateForm.inc',     ['office'    => $office]);
        return $this->template;
	}
}
