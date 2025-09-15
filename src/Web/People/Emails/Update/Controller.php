<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Emails\Update;

use Application\Models\Email;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['email_id'])) {
            try { $email = new Email($params['email_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($email)) { return new \Web\Views\NotFoundView(); }

        parent::captureNewReturnUrl(\Web\View::generateUrl('people.view', ['person_id'=>$email->getPerson_id()]));

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
