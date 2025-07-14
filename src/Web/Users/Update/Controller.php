<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
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
        if (!empty($_REQUEST['person_id'])) {
            try { $person = new Person($_REQUEST['person_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($person)) {
            return new \Web\Views\NotFoundView();
        }

        if (isset($_POST['username'])) {
            $person->handleUpdateUserAccount($_POST);

            try {
                $person->save();
                if (!empty($_POST['email']) && !$person->hasEmail($_POST['email'])) {
                     $person->saveEmail($_POST['email']);
                }

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
