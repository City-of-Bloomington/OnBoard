<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PeopleTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;

class PeopleController extends Controller
{
	public function index()
	{
		$table = new PeopleTable();
		$people = $table->find(null, 'lastname', true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$people->setCurrentPageNumber($page);
		$people->setItemCountPerPage(20);

		$this->template->blocks[] = new Block('people/list.inc',    ['people'   =>$people]);
		$this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$people]);
	}

	public function view()
	{
		try {
			$person = new Person($_REQUEST['person_id']);
			$this->template->blocks[] = new Block('people/info.inc',array('person'=>$person));
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

	}

	public function update()
	{
		if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
			try {
				$person = new Person($_REQUEST['person_id']);
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				header("Location: $errorURL");
				exit();
			}
		}
		else {
			$person = new Person();
		}

		if (isset($_POST['firstname'])) {
			$person->handleUpdate($_POST);
			try {
				$person->save();
				header('Location: '.BASE_URL.'/people');
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('people/updateForm.inc',array('person'=>$person));
	}
}
