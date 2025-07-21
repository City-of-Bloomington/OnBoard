<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Add;

use Application\Models\Person;
use Web\Url;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (isset($_REQUEST['return_url'])) {
            $return_url = new Url($_REQUEST['return_url']);
        }
        elseif (isset($_REQUEST['callback'])) {
            $return_url = new Url(\Web\View::generateUrl('people.callback'));
        }
        else {
            $return_url = new Url(\Web\View::generateUrl('people.index'));
        }

        if (isset($_POST['firstname'])) {
            $person = new Person();
            $person->handleUpdate($_POST);
            try {
                $person->save();
                if (!empty($_POST['email'])) { $person->saveEmail($_POST['email']); }
                if (!empty($_POST['phone'])) { $person->savePhone($_POST['phone']); }

                if (isset($_REQUEST['return_url'])) {
                    $return_url->person_id = $person->getId();
                }
                elseif (isset($_REQUEST['callback'])) {
                    $return_url->person_id = $person->getId();
                }
                else {
                    $return_url = new Url(\Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]));
                }

                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new View((string)$return_url);
    }
}
