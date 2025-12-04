<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Emails\Add;

use Application\Models\Email;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $email = new Email();

        if (!empty($params['person_id'])) {
            try {
                $person = new Person($params['person_id']);
                $email->setPerson($person);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($person)) { return new \Web\Views\NotFoundView(); }


        parent::captureNewReturnUrl(\Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]));

        if (isset($_POST['email'])) {
            $email->handleUpdate($_POST);

            try  {
                $email->save();
                $url = parent::popCurrentReturnUrl();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($email, $_SESSION['return_url']);
    }
}
