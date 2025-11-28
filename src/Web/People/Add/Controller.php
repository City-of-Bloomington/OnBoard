<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Add;

use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $person     = new Person();

        if (isset($_POST['firstname'])) {
            $person->handleUpdate($_POST);
            try {
                $person->save();
                if (!empty($_POST['email'])) { $person->saveEmail($_POST['email']); }
                if (!empty($_POST['phone'])) { $person->savePhone($_POST['phone']); }


                $return_url = \Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]);
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View($person);
    }
}
