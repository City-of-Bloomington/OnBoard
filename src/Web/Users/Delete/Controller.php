<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Users\Delete;

use Application\Models\Person;

class Controller extends \Web\Controller
{
    public function __invoke(array $params): \Web\View
    {
        $return_url = $_REQUEST['return_url'] ?? \Web\View::generateUrl('users.index');

        try {
            $person = new Person($params['id']);
            $person->deleteUserAccount();
            $person->save();
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e->getMessage();
        }
        header("Location: $return_url");
        exit();
    }
}
