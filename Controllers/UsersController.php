<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PeopleTable;
use Blossom\Classes\Controller;
use Blossom\Classes\Block;
use Blossom\Classes\Database;

class UsersController extends Controller
{
	public function index()
	{
		$people = new PeopleTable();
		$users = $people->find(['user_account'=>true], null, true);

		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
		$users->setCurrentPageNumber($page);
		$users->setItemCountPerPage(20);

		$this->template->blocks[] = new Block('users/list.inc',array('users'=>$users));
		$this->template->blocks[] = new Block('pageNavigation.inc', ['paginator'=>$users]);
	}

	public function update()
	{
        if (!empty($_REQUEST['user_id'])) {
            try { $person = new Person($_REQUEST['user_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e; }
        }
        else {
            $person = new Person();
        }

        if (isset($person)) {
            if (isset($_POST['username'])) {
                try {
                    $person->handleUpdateUserAccount($_POST);
                    // We might have populated this person's information from LDAP
                    // We need to do a new lookup in the system, to see if a person
                    // with their email address already exists.
                    // If they already exist, we should add the account info to that
                    // person record.
                    if (!$person->getId() && $person->getEmail()) {
                        try {
                            $existingPerson = new Person($person->getEmail());
                            $existingPerson->handleUpdateUserAccount($_POST);
                        }
                        catch (\Exception $e) { }
                    }

                    if (isset($existingPerson)) { $existingPerson->save(); }
                    else { $person->save(); }

                    header('Location: '.BASE_URL.'/users');
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $this->template->blocks[] = new Block('users/updateForm.inc', ['user'=>$person]);
            if ($person->getId()) {
                $this->template->blocks[] = new Block('people/info.inc', [
                    'person'         => $person,
                    'disableButtons' => true
                ]);
            }
        }
        else {
            header('HTTP/1.1 404 Not Found', true, 404);
            $this->template->blocks[] = new Block('404.inc');
        }
	}

	public function delete()
	{
		try {
			$person = new Person($_REQUEST['user_id']);
			$person->deleteUserAccount();
			$person->save();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header('Location: '.BASE_URL.'/users');
		exit();
	}
}
