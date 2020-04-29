<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PeopleTable;

use Web\Block;
use Web\Controller;
use Web\Url;
use Web\View;

class PeopleController extends Controller
{
    private static function searchFields(): array
    {
        $fields = ['firstname', 'lastname'];
        if (Person::isAllowed('people', 'viewContactInfo')) {
            $fields[] = 'email';
        }
        return $fields;
    }

	public function index(): View
	{
		$table = new PeopleTable();
		$people = $table->search($_GET, 'lastname', true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$people->setCurrentPageNumber($page);
		$people->setItemCountPerPage(20);

        if ($this->template->outputFormat == 'html') {
            $this->template->blocks[] = new Block('people/findForm.inc', ['fields'=>self::searchFields()]);
        }

		if (isset($_GET['firstname'])) {
            $this->template->blocks[] = new Block('people/list.inc',    ['people'   =>$people]);
            if ($this->template->outputFormat == 'html') {
                $this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$people]);
            }
        }
        $this->template->title = $this->template->_(['person', 'people', 2]).' - '.APPLICATION_NAME;
        return $this->template;
	}

	public function parameters(): View
	{
        $this->template->blocks[] = new Block('people/partials/findParameters.inc', ['fields'=>self::searchFields()]);
        return $this->template;
	}

	public function view(): View
	{
        if (!empty($_REQUEST['person_id'])) {
            try { $person = new Person($_REQUEST['person_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }

        if (isset($person)) {
			$this->template->title = $person->getFullname().' - '.APPLICATION_NAME;
			if ($this->template->outputFormat == 'html') {
                $this->template->blocks[] = new Block('people/personView.inc', ['person'=>$person]);
			}
			else {
                $this->template->blocks[] = new Block('people/info.inc', ['person'=>$person]);
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
        return $this->template;
	}

	public function delete(): View
	{
        if (!empty($_REQUEST['person_id'])) {
            try {
                $person = new Person($_REQUEST['person_id']);
                $person->delete();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        header('Location: '.BASE_URL.'/people');
        exit();
        return $this->template;
	}
}
