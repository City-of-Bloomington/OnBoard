<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Phones\Update;

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

        $return_url = \Web\View::generateUrl('profile.index');
        if ($phone->getPerson_id() != $_SESSION['USER']->getId()) {
            header("Location: $return_url");
            exit();
        }

        if (isset($_POST['number'])) {
            $phone->handleUpdate($_POST);

            try  {
                $phone->save();
                header("Location: $return_url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($phone, $return_url);
    }
}
