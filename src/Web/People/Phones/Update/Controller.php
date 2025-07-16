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
        if (empty($_SESSION['return_url'])) {
                  $_SESSION['return_url'] = self::return_url($phone->getPerson_id());
        }

        if (isset($_POST['number'])) {
            $phone->handleUpdate($_POST);

            try  {
                $phone->save();
                $url = $_SESSION['return_url'];
                unset( $_SESSION['return_url'] );
                header("Location: $url");
                exit();
            }
            catch (\Exception $e) { $_SESSION['errorMessages'][] = $e->getMessage(); }
        }

        return new View($phone, $_SESSION['return_url']);
    }

    private static function return_url(int $person_id): string
    {
        return !empty($_REQUEST['return_url'])
                    ? $_REQUEST['return_url']
                    : \Web\View::generateUrl('people.view', ['person_id'=>$person_id]);
    }
}
