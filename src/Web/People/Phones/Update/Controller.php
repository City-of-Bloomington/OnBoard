<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Phones\Update;

use Application\Models\Phone;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['phone_id'])) {
            try { $phone = new Phone($params['phone_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($phone)) { return new \Web\Views\NotFoundView(); }

        parent::captureNewReturnUrl(\Web\View::generateUrl('people.view', ['person_id'=>$phone->getPerson_id()]));

        if (isset($_POST['number'])) {
            $phone->handleUpdate($_POST);

            try  {
                $phone->save();
                $url = parent::popCurrentReturnUrl();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($phone, $_SESSION['return_url']);
    }
}
