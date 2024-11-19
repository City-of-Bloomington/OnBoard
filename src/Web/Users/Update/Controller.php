<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Update;

use Application\Models\Person;
use Application\Models\DepartmentTable;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['id'])) {
            try { $person = new Person($params['id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($person)) {
            return new \Web\Views\NotFoundView();
        }

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

                header('Location: '.\Web\View::generateUrl('users.index'));
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($person);
    }
}
