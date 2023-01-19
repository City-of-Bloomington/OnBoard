<?php
/**
 * @copyright 2012-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;
use Application\Models\PeopleTable;
use Application\Models\DepartmentTable;

use Web\Controller;
use Web\Block;
use Web\Database;
use Web\View;

class UsersController extends Controller
{
	public function index(): View
	{
        $_GET['user_account'] = true;

		$people = new PeopleTable();

        if ($this->template->outputFormat != 'html') {
            $users = $people->search($_GET);
            $this->template->blocks = [new Block('users/list.inc', ['users' => $users])];
            return $this->template;
        }

        $page  = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
        $users = $people->search($_GET, null, true);
        $users->setCurrentPageNumber($page);
        $users->setItemCountPerPage(20);

		global $ACL;
		$departments = new DepartmentTable();

		$title = $this->template->_(['user', 'users', 2]);
		$this->template->title = $title.' - '.APPLICATION_NAME;
		$this->template->blocks = [
            new Block('users/findForm.inc', [
                'departments'           => $departments->find(),
                'roles'                 => $ACL->getRoles(),
                'authenticationMethods' => Person::getAuthenticationMethods()
            ]),
            new Block('users/list.inc',     ['users'    =>$users]),
            new Block('pageNavigation.inc', ['paginator'=>$users])
		];
        return $this->template;
	}

	public function update(): View
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

                    header('Location: '.View::generateUrl('users.index'));
                    exit();
                }
                catch (\Exception $e) {
                    $_SESSION['errorMessages'][] = $e;
                }
            }

            $departments = new DepartmentTable();
            $this->template->blocks[] = new Block('users/updateForm.inc', [
                'user'        => $person,
                'departments' => $departments->find()
            ]);
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
        return $this->template;
	}

	public function delete(): View
	{
        $return_url = $_REQUEST['return_url'] ?? View::generateUrl('users.index');

		try {
			$person = new Person($_REQUEST['user_id']);
			$person->deleteUserAccount();
			$person->save();
		}
		catch (\Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
		header("Location: $return_url");
		exit();
        return $this->template;
	}
}
