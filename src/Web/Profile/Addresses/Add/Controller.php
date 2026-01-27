<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Addresses\Add;

use Application\Models\Address;
use Application\Models\Person;
use Web\Profile\Addresses\Update\View as UpdateView;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $type    = (!empty($_REQUEST['type']) && $_REQUEST['type']=='Home')
                 ? 'Home'
                 : 'Mailing';

        $address = new Address();
        $address->setType($type);
        $address->setPerson($_SESSION['USER']);
        $return_url = \Web\View::generateUrl('profile.index');

        if (isset($_POST['address'])) {
            $address->handleUpdate($_POST);

            try  {
                $address->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new UpdateView($address, $return_url);
    }
}
