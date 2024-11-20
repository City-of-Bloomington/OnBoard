<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
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
        $person = new Person();

        if (isset($_POST['firstname'])) {
            $person->handleUpdate($_POST);
            try {
                $person->save();

                if (isset($_REQUEST['return_url'])) {
                    $return_url = new Url($_REQUEST['return_url']);
                    $return_url->person_id = $person->getId();
                }
                elseif (isset($_REQUEST['callback'])) {
                    $return_url = new Url(\Web\View::generateUrl('people.callback'));
                    $return_url->person_id = $person->getId();
                }
                else {
                    $return_url = $person->getUrl();
                }
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) {
                $_SESSION['errorMessages'][] = $e->getMessage();
            }
        }

        return new \Web\People\Update\View($person);
    }
}
