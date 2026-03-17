<?php
/**
 * @copyright 2024-2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Delete;

use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        try {
            $person = new Person($_REQUEST['person_id']);
            $person->deleteUserAccount();
            $person->save();

            $return_url = \Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]);
            header("Location: $return_url");
            exit();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
        }

        header('Location: '.\Web\View::generateUrl('users.index'));
        exit();
    }
}
