<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Update;

use Application\Models\Person;

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

        $return_url = $_REQUEST['return_url'] ?? \Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]);

        if (isset($_POST['firstname'])) {
            $person->handleUpdate($_POST);
            try {
                $person->save();

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($person, $return_url);
    }
}
