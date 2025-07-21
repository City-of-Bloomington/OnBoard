<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Profile\Phones\Add;

use Application\Models\Phone;
use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $phone = new Phone();
        $phone->setPerson($_SESSION['USER']);


        if (!isset($person)) { return new \Web\Views\NotFoundView(); }

        $return_url = \Web\View::generateUrl('profile.index');

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
