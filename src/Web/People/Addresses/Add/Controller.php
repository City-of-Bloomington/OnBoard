<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\People\Addresses\Add;

use Application\Models\Address;
use Application\Models\Person;
use Web\People\Addresses\Update\View as UpdateView;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $type    = (!empty($_REQUEST['type']) && $_REQUEST['type']=='Home')
                 ? 'Home'
                 : 'Mailing';

        $address = new Address();
        $address->setType($type);

        if (!empty($params['person_id'])) {
            try {
                $person = new Person($params['person_id']);
                $address->setPerson($person);
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        if (!isset($person)) { return new \Web\Views\NotFoundView(); }


        parent::captureNewReturnUrl(\Web\View::generateUrl('people.view', ['person_id'=>$person->getId()]));

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

        return new UpdateView($address, $_SESSION['return_url']);
    }
}
