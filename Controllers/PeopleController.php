<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PeopleTable;
use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Url;

class PeopleController extends Controller
{
	public function index()
	{
		$this->template->blocks[] = new Block('people/findForm.inc');

		$table = new PeopleTable();
		$people = $table->search($_GET, 'lastname', true);

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
			$this->template->blocks[] = new Block('people/info.inc', ['person'=>$person]);
			$this->template->blocks[] = new Block('people/tabs.inc', ['person'=>$person]);
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}

	}

	public function update()
	{
		$errorURL = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/people';

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

				if (isset($_REQUEST['return_url'])) {
					$return_url = new Url($_REQUEST['return_url']);
					$return_url->person_id = $person->getId();
				}
				elseif (isset($_REQUEST['callback'])) {
					$return_url = new Url(BASE_URL.'/callback');
					$return_url->callback = $_REQUEST['callback'];
					$return_url->data = "{$person->getId()}";
				}
				else {
					$return_url = $person->getUrl();
				}
				header("Location: $return_url");
				exit();
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('people/updateForm.inc',array('person'=>$person));
	}
}
