<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Addresses\Update;

use Application\Models\Address;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        if (!empty($params['address_id'])) {
            try { $address = new Address($params['address_id']); }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($address)) { return new \Web\Views\NotFoundView(); }

        parent::captureNewReturnUrl(\Web\View::generateUrl('people.view', ['person_id'=>$address->getPerson_id()]));

        if (isset($_POST['address'])) {
            $address->handleUpdate($_POST);

            try  {
                $address->save();
                $url = parent::popCurrentReturnUrl();
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($address, $_SESSION['return_url']);
    }
}
